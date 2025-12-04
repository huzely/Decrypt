import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { prisma } from '../utils/prisma';
import { env } from '../config/env';
import { v4 as uuid } from 'uuid';

export async function register(username: string, email: string, password: string) {
  const existing = await prisma.user.findFirst({ where: { OR: [{ email }, { username }] } });
  if (existing) throw new Error('User already exists');
  const hashed = await bcrypt.hash(password, 10);
  const user = await prisma.user.create({
    data: {
      username,
      email,
      password: hashed,
      wallet: { create: { balance: 0 } },
    },
  });
  return user;
}

export async function login(email: string, password: string) {
  const user = await prisma.user.findUnique({ where: { email } });
  if (!user) throw new Error('Invalid credentials');
  const match = await bcrypt.compare(password, user.password);
  if (!match) throw new Error('Invalid credentials');
  if (user.status === 'BANNED') throw new Error('Account banned');
  const token = jwt.sign({ userId: user.id, role: user.role }, env.jwtSecret, { expiresIn: '7d' });
  return { token, user };
}

export async function requestPasswordReset(email: string) {
  const user = await prisma.user.findUnique({ where: { email } });
  if (!user) throw new Error('User not found');
  const token = uuid();
  const expires = new Date(Date.now() + 1000 * 60 * 15);
  await prisma.passwordResetToken.create({
    data: { token, userId: user.id, expiresAt: expires },
  });
  return { token, expiresAt: expires };
}

export async function resetPassword(token: string, password: string) {
  const record = await prisma.passwordResetToken.findUnique({ where: { token } });
  if (!record || record.used || record.expiresAt < new Date()) throw new Error('Invalid or expired token');
  const hashed = await bcrypt.hash(password, 10);
  await prisma.user.update({ where: { id: record.userId }, data: { password: hashed } });
  await prisma.passwordResetToken.update({ where: { id: record.id }, data: { used: true } });
  return true;
}

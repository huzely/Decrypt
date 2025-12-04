import { PrismaClient, Role } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  const adminPassword = await bcrypt.hash('Admin@123', 10);
  const userPassword = await bcrypt.hash('User@123', 10);

  const admin = await prisma.user.upsert({
    where: { email: 'admin@example.com' },
    update: {},
    create: {
      email: 'admin@example.com',
      username: 'admin',
      password: adminPassword,
      role: Role.ADMIN,
    },
  });

  const user = await prisma.user.upsert({
    where: { email: 'trader@example.com' },
    update: {},
    create: {
      email: 'trader@example.com',
      username: 'trader',
      password: userPassword,
    },
  });

  await prisma.wallet.upsert({
    where: { userId: admin.id },
    update: {},
    create: { userId: admin.id, balance: 0 },
  });

  await prisma.wallet.upsert({
    where: { userId: user.id },
    update: {},
    create: { userId: user.id, balance: 1000 },
  });

  await prisma.asset.upsert({
    where: { symbol: 'EURUSD' },
    update: {},
    create: {
      name: 'Euro / US Dollar',
      symbol: 'EURUSD',
      manualPayout: 85,
      basePayout: 85,
      minStake: 1,
      maxStake: 2000,
    },
  });

  await prisma.asset.upsert({
    where: { symbol: 'GOLD' },
    update: {},
    create: {
      name: 'Gold Spot',
      symbol: 'GOLD',
      manualPayout: 80,
      basePayout: 82,
      minStake: 1,
      maxStake: 2000,
    },
  });

  await prisma.asset.upsert({
    where: { symbol: 'INDEX01' },
    update: {},
    create: {
      name: 'Index 01',
      symbol: 'INDEX01',
      manualPayout: 88,
      basePayout: 88,
      minStake: 1,
      maxStake: 2000,
    },
  });

  console.log('Seed completed: admin + trader + assets');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });

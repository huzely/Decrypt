import { NextFunction, Request, Response } from 'express';
import jwt from 'jsonwebtoken';
import { env } from '../config/env';
import { prisma } from '../utils/prisma';

export interface AuthRequest extends Request {
  user?: { id: number; role: string };
}

export function authenticate(required = true) {
  return async (req: AuthRequest, res: Response, next: NextFunction) => {
    const token = req.cookies?.token || req.headers.authorization?.replace('Bearer ', '');
    if (!token) {
      if (required) return res.redirect('/login');
      return next();
    }
    try {
      const payload = jwt.verify(token, env.jwtSecret) as { userId: number; role: string };
      const user = await prisma.user.findUnique({ where: { id: payload.userId } });
      if (!user || user.status === 'BANNED') {
        if (required) return res.status(401).redirect('/login');
        return next();
      }
      req.user = { id: user.id, role: user.role };
      res.locals.currentUser = user;
      next();
    } catch (error) {
      console.error(error);
      if (required) return res.status(401).redirect('/login');
      next();
    }
  };
}

export function requireAdmin(req: AuthRequest, res: Response, next: NextFunction) {
  if (req.user?.role !== 'ADMIN') {
    return res.status(403).send('Forbidden');
  }
  return next();
}

import rateLimit from 'express-rate-limit';
import { env } from '../config/env';

export const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: env.loginRateLimit,
  message: 'Too many login attempts, try later.',
});

export const tradeLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: env.tradeRateLimit,
  message: 'Too many trade attempts, slow down.',
});

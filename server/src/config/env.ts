import dotenv from 'dotenv';

dotenv.config();

export const env = {
  port: parseInt(process.env.PORT || '3000', 10),
  nodeEnv: process.env.NODE_ENV || 'development',
  jwtSecret: process.env.JWT_SECRET || 'secret',
  sessionSecret: process.env.SESSION_SECRET || 'session',
  baseUrl: process.env.BASE_URL || 'http://localhost:3000',
  databaseUrl: process.env.DATABASE_URL || '',
  loginRateLimit: parseInt(process.env.RATE_LIMIT_LOGIN || '10', 10),
  tradeRateLimit: parseInt(process.env.RATE_LIMIT_TRADE || '30', 10),
  httpsEnabled: process.env.HTTPS_ENABLED === 'true',
};

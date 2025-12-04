import { Request, Response } from 'express';
import { placeOrder } from '../services/tradeService';
import { tradeLimiter } from '../middlewares/rateLimit';

export const tradeLimitMiddleware = tradeLimiter;

export async function createOrder(req: Request, res: Response) {
  const { assetId, direction, stake, expiry } = req.body;
  const userId = (req as any).user.id;
  try {
    await placeOrder(userId, parseInt(assetId, 10), direction, parseFloat(stake), parseInt(expiry, 10));
    res.redirect('/trade');
  } catch (error: any) {
    const assets = await (await import('../utils/prisma')).prisma.asset.findMany({ where: { active: true } });
    res.render('user/trade', { title: 'Giao dịch', assets, error: error.message });
  }
}

import { Request, Response } from 'express';
import { createDeposit, createWithdraw } from '../services/walletService';

export async function requestDeposit(req: Request, res: Response) {
  const { amount } = req.body;
  const userId = (req as any).user.id;
  try {
    await createDeposit(userId, parseFloat(amount));
    res.redirect('/wallet');
  } catch (error: any) {
    res.render('user/wallet', { title: 'Ví', error: error.message });
  }
}

export async function requestWithdraw(req: Request, res: Response) {
  const { amount } = req.body;
  const userId = (req as any).user.id;
  try {
    await createWithdraw(userId, parseFloat(amount));
    res.redirect('/wallet');
  } catch (error: any) {
    res.render('user/wallet', { title: 'Ví', error: error.message });
  }
}

import { Request, Response } from 'express';
import { prisma } from '../utils/prisma';
import { getWallet, getTransactions } from '../services/walletService';
import { getOpenOrders, getOrderHistory } from '../services/tradeService';

export async function dashboard(req: Request, res: Response) {
  const userId = (req as any).user.id;
  const wallet = await getWallet(userId);
  const orders = await getOpenOrders(userId);
  res.render('user/dashboard', { title: 'Dashboard', wallet, orders });
}

export async function profile(req: Request, res: Response) {
  res.render('user/profile', { title: 'Hồ sơ' });
}

export async function walletPage(req: Request, res: Response) {
  const userId = (req as any).user.id;
  const wallet = await getWallet(userId);
  const transactions = await getTransactions(userId);
  res.render('user/wallet', { title: 'Ví', wallet, transactions });
}

export async function tradePage(_req: Request, res: Response) {
  const assets = await prisma.asset.findMany({ where: { active: true } });
  res.render('user/trade', { title: 'Giao dịch', assets });
}

export async function historyPage(req: Request, res: Response) {
  const userId = (req as any).user.id;
  const orders = await getOrderHistory(userId);
  res.render('user/history', { title: 'Lịch sử', orders });
}

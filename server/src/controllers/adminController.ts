import { Request, Response } from 'express';
import { login } from '../services/authService';
import { prisma } from '../utils/prisma';
import { adminHandleTransaction } from '../services/walletService';
import { updatePayoutMode } from '../services/payoutService';

export async function adminLoginPage(_req: Request, res: Response) {
  res.render('admin/login', { title: 'Admin Login', layout: 'admin' });
}

export async function adminHandleLogin(req: Request, res: Response) {
  const { email, password } = req.body;
  try {
    const { token, user } = await login(email, password);
    if (user.role !== 'ADMIN') throw new Error('Not admin');
    res.cookie('token', token, { httpOnly: true });
    res.redirect('/admin');
  } catch (error: any) {
    res.render('admin/login', { title: 'Admin Login', error: error.message, layout: 'admin' });
  }
}

export async function adminDashboard(_req: Request, res: Response) {
  const [userCount, txSummary, orderSummary] = await Promise.all([
    prisma.user.count(),
    prisma.transaction.groupBy({
      by: ['type'],
      _sum: { amount: true },
    }),
    prisma.order.groupBy({ by: ['status'], _count: { _all: true } }),
  ]);
  res.render('admin/dashboard', { title: 'Admin Dashboard', layout: 'admin', userCount, txSummary, orderSummary });
}

export async function listUsers(req: Request, res: Response) {
  const q = (req.query.q as string) || '';
  const users = await prisma.user.findMany({
    where: { OR: [{ username: { contains: q } }, { email: { contains: q } }] },
    include: { wallet: true },
    orderBy: { createdAt: 'desc' },
  });
  res.render('admin/users', { title: 'Người dùng', layout: 'admin', users, q });
}

export async function toggleUserStatus(req: Request, res: Response) {
  const id = parseInt(req.params.id, 10);
  const user = await prisma.user.findUnique({ where: { id } });
  if (user) {
    await prisma.user.update({ where: { id }, data: { status: user.status === 'ACTIVE' ? 'BANNED' : 'ACTIVE' } });
  }
  res.redirect('/admin/users');
}

export async function depositsPage(_req: Request, res: Response) {
  const txs = await prisma.transaction.findMany({ where: { type: 'DEPOSIT' }, include: { user: true }, orderBy: { createdAt: 'desc' } });
  res.render('admin/deposits', { title: 'Nạp tiền', layout: 'admin', txs });
}

export async function withdrawalsPage(_req: Request, res: Response) {
  const txs = await prisma.transaction.findMany({ where: { type: 'WITHDRAW' }, include: { user: true }, orderBy: { createdAt: 'desc' } });
  res.render('admin/withdrawals', { title: 'Rút tiền', layout: 'admin', txs });
}

export async function handleTx(req: Request, res: Response) {
  const id = parseInt(req.params.id, 10);
  const approve = req.body.action === 'approve';
  const adminId = (req as any).user.id;
  await adminHandleTransaction(id, approve, adminId, req.body.note);
  res.redirect(req.headers.referer || '/admin');
}

export async function assetsPage(_req: Request, res: Response) {
  const assets = await prisma.asset.findMany({ orderBy: { createdAt: 'desc' } });
  res.render('admin/assets', { title: 'Tài sản', layout: 'admin', assets });
}

export async function saveAsset(req: Request, res: Response) {
  const { id, name, symbol, manualPayout, minStake, maxStake, active } = req.body;
  if (id) {
    await prisma.asset.update({
      where: { id: parseInt(id, 10) },
      data: {
        name,
        symbol,
        manualPayout: parseInt(manualPayout, 10),
        minStake: parseFloat(minStake),
        maxStake: parseFloat(maxStake),
        active: active === 'on',
      },
    });
  } else {
    await prisma.asset.create({
      data: {
        name,
        symbol,
        manualPayout: parseInt(manualPayout, 10),
        minStake: parseFloat(minStake),
        maxStake: parseFloat(maxStake),
      },
    });
  }
  res.redirect('/admin/assets');
}

export async function payoutPage(req: Request, res: Response) {
  const asset = await prisma.asset.findUnique({ where: { id: parseInt(req.params.id, 10) } });
  res.render('admin/payout', { title: 'Payout', layout: 'admin', asset });
}

export async function savePayout(req: Request, res: Response) {
  const assetId = parseInt(req.params.id, 10);
  const { mode, manualPayout, basePayout, minPayout, maxPayout, volumeThreshold } = req.body;
  await updatePayoutMode(assetId, mode, manualPayout ? parseInt(manualPayout, 10) : undefined, {
    basePayout: basePayout ? parseInt(basePayout, 10) : undefined,
    minPayout: minPayout ? parseInt(minPayout, 10) : undefined,
    maxPayout: maxPayout ? parseInt(maxPayout, 10) : undefined,
    volumeThreshold: volumeThreshold ? parseFloat(volumeThreshold) : undefined,
  });
  res.redirect('/admin/assets');
}

import { prisma } from '../utils/prisma';
import { getCurrentPrice } from './priceService';
import { getPayoutForAsset } from './payoutService';

export async function placeOrder(userId: number, assetId: number, direction: 'UP' | 'DOWN', stake: number, durationSec: number) {
  if (stake <= 0) throw new Error('Stake must be positive');
  const asset = await prisma.asset.findUnique({ where: { id: assetId, active: true } });
  if (!asset) throw new Error('Asset not available');
  if (stake < Number(asset.minStake) || stake > Number(asset.maxStake)) throw new Error('Stake out of bounds');

  const wallet = await prisma.wallet.findUnique({ where: { userId } });
  if (!wallet || Number(wallet.balance) < stake) throw new Error('Insufficient balance');

  const payout = await getPayoutForAsset(assetId);
  const entryPrice = getCurrentPrice(asset.symbol);
  const expireAt = new Date(Date.now() + durationSec * 1000);

  return prisma.$transaction([
    prisma.wallet.update({ where: { userId }, data: { balance: { decrement: stake } } }),
    prisma.order.create({
      data: {
        userId,
        assetId,
        direction,
        stake,
        payout,
        entryPrice,
        expireAt,
      },
    }),
  ]);
}

export async function getOpenOrders(userId: number) {
  return prisma.order.findMany({
    where: { userId, status: 'OPEN' },
    include: { asset: true },
    orderBy: { expireAt: 'asc' },
  });
}

export async function getOrderHistory(userId: number) {
  return prisma.order.findMany({
    where: { userId, status: { not: 'OPEN' } },
    include: { asset: true },
    orderBy: { createdAt: 'desc' },
  });
}

export async function settleDueOrders() {
  const now = new Date();
  const dueOrders = await prisma.order.findMany({ where: { status: 'OPEN', expireAt: { lte: now } }, include: { asset: true } });
  for (const order of dueOrders) {
    const price = getCurrentPrice(order.asset.symbol);
    let status: 'WIN' | 'LOSE' | 'DRAW' = 'LOSE';
    if (order.direction === 'UP' && price > Number(order.entryPrice)) status = 'WIN';
    if (order.direction === 'DOWN' && price < Number(order.entryPrice)) status = 'WIN';
    if (price === Number(order.entryPrice)) status = 'DRAW';

    let profit = 0;
    if (status === 'WIN') profit = Number(order.stake) * (order.payout / 100);
    if (status === 'DRAW') profit = Number(order.stake);

    await prisma.$transaction([
      prisma.order.update({ where: { id: order.id }, data: { status, settlePrice: price, profit } }),
      prisma.wallet.update({ where: { userId: order.userId }, data: { balance: { increment: profit } } }),
    ]);
  }
}

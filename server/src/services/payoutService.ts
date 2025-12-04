import { prisma } from '../utils/prisma';

export async function getPayoutForAsset(assetId: number) {
  const asset = await prisma.asset.findUnique({ where: { id: assetId } });
  if (!asset) throw new Error('Asset not found');
  if (asset.payoutMode === 'MANUAL') return asset.manualPayout;

  // simple volume-based rule
  const since = new Date(Date.now() - 15 * 60 * 1000);
  const volume = await prisma.order.aggregate({
    _sum: { stake: true },
    where: { assetId, createdAt: { gte: since } },
  });
  const volumeValue = Number(volume._sum.stake || 0);
  if (volumeValue > Number(asset.volumeThreshold)) {
    return Math.max(asset.minPayout, Math.min(asset.basePayout - 5, asset.maxPayout));
  }
  return Math.min(asset.maxPayout, Math.max(asset.basePayout + 5, asset.minPayout));
}

export async function updatePayoutMode(assetId: number, mode: 'MANUAL' | 'AUTO', manualPayout?: number, rule?: Partial<{ basePayout: number; minPayout: number; maxPayout: number; volumeThreshold: number }>) {
  return prisma.asset.update({
    where: { id: assetId },
    data: {
      payoutMode: mode,
      manualPayout: manualPayout ?? undefined,
      basePayout: rule?.basePayout ?? undefined,
      minPayout: rule?.minPayout ?? undefined,
      maxPayout: rule?.maxPayout ?? undefined,
      volumeThreshold: rule?.volumeThreshold ?? undefined,
    },
  });
}

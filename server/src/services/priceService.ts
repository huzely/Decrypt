import { prisma } from '../utils/prisma';

const priceState: Record<string, number> = {};

function randomWalk(base: number) {
  const change = (Math.random() - 0.5) * 0.2;
  return Math.max(0.1, parseFloat((base + change).toFixed(4)));
}

export async function loadInitialPrices() {
  const assets = await prisma.asset.findMany({ where: { active: true } });
  assets.forEach((asset) => {
    priceState[asset.symbol] = priceState[asset.symbol] || 100 + Math.random() * 10;
  });
}

export function getCurrentPrice(symbol: string) {
  if (!priceState[symbol]) priceState[symbol] = 100 + Math.random() * 10;
  priceState[symbol] = randomWalk(priceState[symbol]);
  return priceState[symbol];
}

export function getAllPrices() {
  return Object.entries(priceState).map(([symbol, price]) => ({ symbol, price }));
}

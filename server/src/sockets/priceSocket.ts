import { Server } from 'socket.io';
import { getAllPrices, getCurrentPrice } from '../services/priceService';
import { prisma } from '../utils/prisma';

export function initPriceSocket(io: Server) {
  io.on('connection', async (socket) => {
    const assets = await prisma.asset.findMany({ where: { active: true } });
    socket.emit('prices', getAllPrices());

    const interval = setInterval(() => {
      const updates = assets.map((asset) => ({ symbol: asset.symbol, price: getCurrentPrice(asset.symbol) }));
      socket.emit('prices', updates);
    }, 2000);

    socket.on('disconnect', () => clearInterval(interval));
  });
}

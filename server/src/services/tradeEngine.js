const { Trade, Wallet, Asset } = require('../models');
const priceFeed = require('./priceFeed');

class TradeEngine {
  constructor() {
    this.activeTimers = new Map();
  }

  async schedule(trade) {
    const delay = Math.max(0, trade.expiresAt.getTime() - Date.now());
    const timeout = setTimeout(async () => {
      await this.resolveTrade(trade.id);
    }, delay);
    this.activeTimers.set(trade.id, timeout);
  }

  async resolveTrade(tradeId) {
    const trade = await Trade.findByPk(tradeId, { include: [Wallet, Asset] });
    if (!trade || trade.status !== 'open') return;
    const finalPrice = priceFeed.getPrice(trade.Asset.symbol) + (trade.Asset.bias || 0);
    let status = 'draw';
    if (finalPrice > trade.entryPrice && trade.direction === 'CALL') status = 'won';
    else if (finalPrice < trade.entryPrice && trade.direction === 'PUT') status = 'won';
    else if (finalPrice !== trade.entryPrice) status = 'lost';

    trade.exitPrice = finalPrice;
    trade.status = status;
    trade.payout = status === 'won' ? Number(trade.amount) * trade.Asset.payout : 0;
    await trade.save();

    if (status === 'won') {
      trade.Wallet.balance = Number(trade.Wallet.balance) + trade.payout;
    } else if (status === 'draw') {
      trade.Wallet.balance = Number(trade.Wallet.balance) + Number(trade.amount);
    }
    await trade.Wallet.save();
    this.activeTimers.delete(tradeId);
    return trade;
  }
}

module.exports = new TradeEngine();

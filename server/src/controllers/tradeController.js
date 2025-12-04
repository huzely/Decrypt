const { v4: uuidv4 } = require('uuid');
const { Trade, Asset, Wallet } = require('../models');
const priceFeed = require('../services/priceFeed');
const tradeEngine = require('../services/tradeEngine');

async function listAssets(req, res) {
  const assets = await Asset.findAll({ order: [['symbol', 'ASC']] });
  res.json(assets);
}

async function placeTrade(req, res) {
  const { assetSymbol, amount, direction, expirySeconds } = req.body;
  const asset = await Asset.findOne({ where: { symbol: assetSymbol, enabled: true } });
  if (!asset) return res.status(404).json({ message: 'Asset not available' });
  const wallet = await Wallet.findOne({ where: { UserId: req.user.id } });
  if (Number(wallet.balance) < Number(amount)) return res.status(400).json({ message: 'Insufficient balance' });
  wallet.balance = Number(wallet.balance) - Number(amount);
  await wallet.save();
  const entryPrice = priceFeed.getPrice(asset.symbol);
  const expiresAt = new Date(Date.now() + Number(expirySeconds || 60) * 1000);
  const trade = await Trade.create({
    id: uuidv4(),
    direction,
    amount,
    entryPrice,
    expiresAt,
    AssetId: asset.id,
    UserId: req.user.id,
    WalletId: wallet.id,
  });
  await tradeEngine.schedule(trade);
  res.json({ message: 'Trade placed', trade });
}

async function openTrades(req, res) {
  const trades = await Trade.findAll({ where: { UserId: req.user.id, status: 'open' }, order: [['expiresAt', 'ASC']] });
  res.json(trades);
}

async function history(req, res) {
  const trades = await Trade.findAll({ where: { UserId: req.user.id }, order: [['createdAt', 'DESC']] });
  res.json(trades);
}

module.exports = { listAssets, placeTrade, openTrades, history };

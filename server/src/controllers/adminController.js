const { User, Transaction, Wallet, Asset, Trade } = require('../models');

async function listUsers(req, res) {
  const users = await User.findAll({ include: [Wallet] });
  res.json(users);
}

async function updateUser(req, res) {
  const { userId } = req.params;
  const { email, username, role } = req.body;
  const user = await User.findByPk(userId);
  if (!user) return res.status(404).json({ message: 'User not found' });
  if (email) user.email = email;
  if (username) user.username = username;
  if (role) user.role = role;
  await user.save();
  res.json(user);
}

async function listTransactions(req, res) {
  const { type } = req.query;
  const where = type ? { type } : {};
  const transactions = await Transaction.findAll({ where, include: [Wallet, User], order: [['createdAt', 'DESC']] });
  res.json(transactions);
}

async function approveTransaction(req, res) {
  const { id } = req.params;
  const { status, note } = req.body;
  const transaction = await Transaction.findByPk(id, { include: [Wallet] });
  if (!transaction) return res.status(404).json({ message: 'Not found' });
  transaction.status = status;
  transaction.note = note;
  await transaction.save();
  if (transaction.type === 'deposit' && status === 'approved') {
    transaction.Wallet.balance = Number(transaction.Wallet.balance) + Number(transaction.amount);
    await transaction.Wallet.save();
  }
  res.json(transaction);
}

async function manageAsset(req, res) {
  const { symbol, enabled, payout, bias } = req.body;
  let asset = await Asset.findOne({ where: { symbol } });
  if (!asset) asset = await Asset.create({ symbol });
  if (enabled !== undefined) asset.enabled = enabled;
  if (payout !== undefined) asset.payout = payout;
  if (bias !== undefined) asset.bias = bias;
  await asset.save();
  res.json(asset);
}

async function report(req, res) {
  const totalDeposit = await Transaction.sum('amount', { where: { type: 'deposit', status: 'approved' } });
  const totalWithdraw = await Transaction.sum('amount', { where: { type: 'withdraw', status: 'approved' } });
  const totalVolume = await Trade.sum('amount');
  res.json({
    totalDeposit: totalDeposit || 0,
    totalWithdraw: totalWithdraw || 0,
    totalVolume: totalVolume || 0,
  });
}

module.exports = { listUsers, updateUser, listTransactions, approveTransaction, manageAsset, report };

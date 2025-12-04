const { Transaction, Wallet } = require('../models');
const { v4: uuidv4 } = require('uuid');

async function getBalance(req, res) {
  const wallet = await Wallet.findOne({ where: { UserId: req.user.id } });
  res.json({ balance: wallet?.balance || 0, currency: wallet?.currency || 'USDT' });
}

async function createDeposit(req, res) {
  const { amount, reference } = req.body;
  const wallet = await Wallet.findOne({ where: { UserId: req.user.id } });
  const transaction = await Transaction.create({
    id: uuidv4(),
    type: 'deposit',
    amount,
    reference,
    WalletId: wallet.id,
    UserId: req.user.id,
  });
  res.json({ message: 'Deposit request created', transaction });
}

async function requestWithdraw(req, res) {
  const { amount, note } = req.body;
  const wallet = await Wallet.findOne({ where: { UserId: req.user.id } });
  if (Number(wallet.balance) < Number(amount)) return res.status(400).json({ message: 'Insufficient balance' });
  wallet.balance = Number(wallet.balance) - Number(amount);
  await wallet.save();
  const transaction = await Transaction.create({
    id: uuidv4(),
    type: 'withdraw',
    amount,
    note,
    WalletId: wallet.id,
    UserId: req.user.id,
  });
  res.json({ message: 'Withdrawal request submitted', transaction });
}

async function history(req, res) {
  const transactions = await Transaction.findAll({ where: { UserId: req.user.id }, order: [['createdAt', 'DESC']] });
  res.json(transactions);
}

module.exports = { getBalance, createDeposit, requestWithdraw, history };

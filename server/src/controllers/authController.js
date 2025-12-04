const { v4: uuidv4 } = require('uuid');
const { User, Wallet } = require('../models');
const { sign } = require('../utils/jwt');
const bcrypt = require('bcryptjs');

async function register(req, res) {
  const { email, username, password } = req.body;
  if (!email || !username || !password) return res.status(400).json({ message: 'Missing fields' });
  const existing = await User.findOne({ where: { email } });
  if (existing) return res.status(400).json({ message: 'Email already registered' });
  const user = await User.create({ id: uuidv4(), email, username, password, role: 'user' });
  await Wallet.create({ UserId: user.id, balance: 0 });
  const token = sign({ id: user.id, role: user.role });
  res.json({ token });
}

async function login(req, res) {
  const { email, password } = req.body;
  const user = await User.findOne({ where: { email } });
  if (!user) return res.status(400).json({ message: 'Invalid credentials' });
  const match = await user.comparePassword(password);
  if (!match) return res.status(400).json({ message: 'Invalid credentials' });
  const token = sign({ id: user.id, role: user.role });
  res.json({ token });
}

async function profile(req, res) {
  const user = req.user;
  res.json({ email: user.email, username: user.username, role: user.role });
}

async function changePassword(req, res) {
  const { oldPassword, newPassword } = req.body;
  const user = req.user;
  const match = await user.comparePassword(oldPassword);
  if (!match) return res.status(400).json({ message: 'Old password incorrect' });
  user.password = newPassword;
  await user.save();
  res.json({ message: 'Password updated' });
}

async function forgotPassword(req, res) {
  const { email } = req.body;
  const user = await User.findOne({ where: { email } });
  if (!user) return res.status(404).json({ message: 'User not found' });
  user.resetToken = sign({ id: user.id, reset: true });
  await user.save();
  res.json({ message: 'Reset token generated (stub)', resetToken: user.resetToken });
}

async function resetPassword(req, res) {
  const { token, newPassword } = req.body;
  const user = await User.findOne({ where: { resetToken: token } });
  if (!user) return res.status(400).json({ message: 'Invalid token' });
  user.password = newPassword;
  user.resetToken = null;
  await user.save();
  res.json({ message: 'Password reset' });
}

module.exports = { register, login, profile, changePassword, forgotPassword, resetPassword };

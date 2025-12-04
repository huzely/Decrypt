const sequelize = require('../config/database');
const User = require('./User');
const Wallet = require('./Wallet');
const Transaction = require('./Transaction');
const Asset = require('./Asset');
const Trade = require('./Trade');

User.hasOne(Wallet, { onDelete: 'CASCADE' });
Wallet.belongsTo(User);

User.hasMany(Transaction);
Transaction.belongsTo(User);
Wallet.hasMany(Transaction);
Transaction.belongsTo(Wallet);

Asset.hasMany(Trade);
Trade.belongsTo(Asset);

User.hasMany(Trade);
Trade.belongsTo(User);

Wallet.hasMany(Trade);
Trade.belongsTo(Wallet);

module.exports = {
  sequelize,
  User,
  Wallet,
  Transaction,
  Asset,
  Trade,
};

const { DataTypes, Model } = require('sequelize');
const sequelize = require('../config/database');

class Wallet extends Model {}

Wallet.init(
  {
    id: {
      type: DataTypes.UUID,
      primaryKey: true,
      defaultValue: DataTypes.UUIDV4,
    },
    balance: {
      type: DataTypes.DECIMAL(18, 8),
      defaultValue: 0,
      allowNull: false,
    },
    currency: {
      type: DataTypes.STRING,
      defaultValue: 'USDT',
    },
  },
  { sequelize, modelName: 'Wallet' }
);

module.exports = Wallet;

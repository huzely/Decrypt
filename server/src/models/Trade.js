const { DataTypes, Model } = require('sequelize');
const sequelize = require('../config/database');

class Trade extends Model {}

Trade.init(
  {
    id: {
      type: DataTypes.UUID,
      primaryKey: true,
      defaultValue: DataTypes.UUIDV4,
    },
    direction: {
      type: DataTypes.ENUM('CALL', 'PUT'),
      allowNull: false,
    },
    amount: {
      type: DataTypes.DECIMAL(18, 8),
      allowNull: false,
    },
    entryPrice: {
      type: DataTypes.FLOAT,
      allowNull: false,
    },
    exitPrice: {
      type: DataTypes.FLOAT,
    },
    expiresAt: {
      type: DataTypes.DATE,
      allowNull: false,
    },
    status: {
      type: DataTypes.ENUM('open', 'won', 'lost', 'draw'),
      defaultValue: 'open',
    },
    payout: {
      type: DataTypes.FLOAT,
      defaultValue: 0,
    },
  },
  { sequelize, modelName: 'Trade' }
);

module.exports = Trade;

const { DataTypes, Model } = require('sequelize');
const sequelize = require('../config/database');

class Asset extends Model {}

Asset.init(
  {
    id: {
      type: DataTypes.UUID,
      primaryKey: true,
      defaultValue: DataTypes.UUIDV4,
    },
    symbol: {
      type: DataTypes.STRING,
      unique: true,
      allowNull: false,
    },
    enabled: {
      type: DataTypes.BOOLEAN,
      defaultValue: true,
    },
    payout: {
      type: DataTypes.FLOAT,
      defaultValue: 0.8,
    },
    bias: {
      type: DataTypes.FLOAT,
      defaultValue: 0,
    },
  },
  { sequelize, modelName: 'Asset' }
);

module.exports = Asset;

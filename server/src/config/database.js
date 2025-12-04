const { Sequelize } = require('sequelize');
require('dotenv').config();

const dialect = process.env.DB_DIALECT || 'sqlite';
const sequelize = new Sequelize(
  process.env.DB_NAME || 'bo_platform',
  process.env.DB_USER || 'root',
  process.env.DB_PASSWORD || '',
  {
    host: process.env.DB_HOST || 'localhost',
    dialect,
    logging: false,
    storage: dialect === 'sqlite' ? 'data.db' : undefined,
  }
);

module.exports = sequelize;

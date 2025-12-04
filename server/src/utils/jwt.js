const jwt = require('jsonwebtoken');
require('dotenv').config();

const JWT_SECRET = process.env.JWT_SECRET || 'changeme-secret';
const JWT_EXPIRES = process.env.JWT_EXPIRES || '7d';

module.exports = {
  sign(payload) {
    return jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES });
  },
  verify(token) {
    return jwt.verify(token, JWT_SECRET);
  },
};

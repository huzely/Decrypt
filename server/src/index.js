require('dotenv').config();
const express = require('express');
const cors = require('cors');
const morgan = require('morgan');
const cookieParser = require('cookie-parser');
const http = require('http');
const { Server } = require('socket.io');
const { sequelize, Asset } = require('./models');
const priceFeed = require('./services/priceFeed');
const tradeEngine = require('./services/tradeEngine');

const authRoutes = require('./routes/authRoutes');
const walletRoutes = require('./routes/walletRoutes');
const tradeRoutes = require('./routes/tradeRoutes');
const adminRoutes = require('./routes/adminRoutes');

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

app.use(cors());
app.use(express.json());
app.use(cookieParser());
app.use(morgan('dev'));

app.get('/', (req, res) => res.json({ status: 'ok' }));
app.use('/api/auth', authRoutes);
app.use('/api/wallet', walletRoutes);
app.use('/api/trade', tradeRoutes);
app.use('/api/admin', adminRoutes);

io.on('connection', (socket) => {
  const listener = ({ symbol, price }) => socket.emit('price', { symbol, price });
  priceFeed.on('price', listener);
  socket.on('disconnect', () => priceFeed.off('price', listener));
});

async function bootstrap() {
  await sequelize.sync();
  const defaults = ['EUR/USD', 'BTC/USD', 'ETH/USD'];
  await Promise.all(
    defaults.map(async (symbol) => {
      const existing = await Asset.findOne({ where: { symbol } });
      if (!existing) await Asset.create({ symbol, payout: 0.8 });
    })
  );
  await priceFeed.init();
  console.log('Database synced');
}

const PORT = process.env.PORT || 4000;
bootstrap()
  .then(() => {
    server.listen(PORT, () => console.log(`API listening on ${PORT}`));
  })
  .catch((err) => {
    console.error('Bootstrap error', err);
  });

module.exports = { app, server, io };

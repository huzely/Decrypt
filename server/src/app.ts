import express from 'express';
import path from 'path';
import cookieParser from 'cookie-parser';
import morgan from 'morgan';
import helmet from 'helmet';
import http from 'http';
import { Server } from 'socket.io';
import { env } from './config/env';
import authRoutes from './routes/authRoutes';
import userRoutes from './routes/userRoutes';
import adminRoutes from './routes/adminRoutes';
import { loadInitialPrices } from './services/priceService';
import { initPriceSocket } from './sockets/priceSocket';
import { settleDueOrders } from './services/tradeService';
import { errorHandler } from './middlewares/errorHandler';
import { authenticate } from './middlewares/auth';

const app = express();
const server = http.createServer(app);
const io = new Server(server);

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());
app.use(morgan('dev'));
app.use(helmet());
app.use('/public', express.static(path.join(__dirname, 'public')));

app.use((req, res, next) => {
  res.locals.currentUser = res.locals.currentUser || null;
  next();
});

app.get('/', authenticate(false), (req, res) => {
  if ((req as any).user) return res.redirect('/dashboard');
  return res.redirect('/login');
});

app.use('/', authRoutes);
app.use('/', userRoutes);
app.use('/admin', adminRoutes);

app.use(errorHandler);

async function bootstrap() {
  await loadInitialPrices();
  initPriceSocket(io);
  setInterval(() => settleDueOrders(), 2000);

  server.listen(env.port, () => {
    console.log(`Server listening on port ${env.port}`);
  });
}

bootstrap();

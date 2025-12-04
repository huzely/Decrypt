import { Router } from 'express';
import { authenticate } from '../middlewares/auth';
import { dashboard, historyPage, profile, tradePage, walletPage } from '../controllers/userController';
import { createOrder, tradeLimitMiddleware } from '../controllers/tradeController';
import { requestDeposit, requestWithdraw } from '../controllers/walletController';

const router = Router();

router.use(authenticate());

router.get('/dashboard', dashboard);
router.get('/profile', profile);
router.get('/wallet', walletPage);
router.post('/wallet/deposit', requestDeposit);
router.post('/wallet/withdraw', requestWithdraw);
router.get('/trade', tradePage);
router.post('/trade', tradeLimitMiddleware, createOrder);
router.get('/history', historyPage);

export default router;

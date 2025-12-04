const express = require('express');
const router = express.Router();
const { authenticate } = require('../middleware/auth');
const controller = require('../controllers/walletController');

router.use(authenticate);
router.get('/balance', controller.getBalance);
router.post('/deposit', controller.createDeposit);
router.post('/withdraw', controller.requestWithdraw);
router.get('/history', controller.history);

module.exports = router;

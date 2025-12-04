const express = require('express');
const router = express.Router();
const { authenticate } = require('../middleware/auth');
const { tradeLimiter } = require('../middleware/rateLimiters');
const controller = require('../controllers/tradeController');

router.use(authenticate);
router.get('/assets', controller.listAssets);
router.post('/place', tradeLimiter, controller.placeTrade);
router.get('/open', controller.openTrades);
router.get('/history', controller.history);

module.exports = router;

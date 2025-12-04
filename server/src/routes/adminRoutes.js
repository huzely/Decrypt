const express = require('express');
const router = express.Router();
const { authenticate, authorizeAdmin } = require('../middleware/auth');
const controller = require('../controllers/adminController');

router.use(authenticate, authorizeAdmin);
router.get('/users', controller.listUsers);
router.put('/users/:userId', controller.updateUser);
router.get('/transactions', controller.listTransactions);
router.post('/transactions/:id/decision', controller.approveTransaction);
router.post('/assets', controller.manageAsset);
router.get('/report', controller.report);

module.exports = router;

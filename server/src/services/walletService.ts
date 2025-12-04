import { prisma } from '../utils/prisma';

export async function getWallet(userId: number) {
  return prisma.wallet.findUnique({ where: { userId } });
}

export async function getTransactions(userId: number) {
  return prisma.transaction.findMany({ where: { userId }, orderBy: { createdAt: 'desc' } });
}

export async function createDeposit(userId: number, amount: number) {
  if (amount <= 0) throw new Error('Amount must be positive');
  return prisma.transaction.create({
    data: { userId, amount, type: 'DEPOSIT' },
  });
}

export async function createWithdraw(userId: number, amount: number) {
  const wallet = await getWallet(userId);
  if (!wallet || Number(wallet.balance) < amount) throw new Error('Insufficient balance');
  return prisma.transaction.create({
    data: { userId, amount, type: 'WITHDRAW' },
  });
}

export async function adminHandleTransaction(id: number, approve: boolean, adminId: number, note?: string) {
  const tx = await prisma.transaction.findUnique({ where: { id } });
  if (!tx) throw new Error('Transaction not found');
  if (tx.status !== 'PENDING') throw new Error('Already handled');

  if (approve) {
    if (tx.type === 'DEPOSIT') {
      await prisma.$transaction([
        prisma.wallet.update({ where: { userId: tx.userId }, data: { balance: { increment: tx.amount } } }),
        prisma.transaction.update({
          where: { id },
          data: { status: 'COMPLETED', handledBy: adminId, note },
        }),
      ]);
    } else {
      await prisma.$transaction([
        prisma.wallet.update({ where: { userId: tx.userId }, data: { balance: { decrement: tx.amount } } }),
        prisma.transaction.update({ where: { id }, data: { status: 'COMPLETED', handledBy: adminId, note } }),
      ]);
    }
  } else {
    await prisma.transaction.update({ where: { id }, data: { status: 'REJECTED', handledBy: adminId, note } });
  }
  return prisma.transaction.findUnique({ where: { id } });
}

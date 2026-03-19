import { Video } from '../models/Video.js';
import { ViewLog } from '../models/ViewLog.js';

export async function getOverview(req, res) {
  const [totalVideos, totalViewsAgg] = await Promise.all([
    Video.countDocuments(),
    Video.aggregate([{ $group: { _id: null, totalViews: { $sum: '$views' } } }])
  ]);

  res.json({
    totalVideos,
    totalViews: totalViewsAgg[0]?.totalViews || 0
  });
}

export async function getDaily(req, res) {
  const data = await ViewLog.aggregate([
    {
      $group: {
        _id: {
          year: { $year: '$createdAt' },
          month: { $month: '$createdAt' },
          day: { $dayOfMonth: '$createdAt' }
        },
        count: { $sum: 1 }
      }
    },
    { $sort: { '_id.year': 1, '_id.month': 1, '_id.day': 1 } }
  ]);

  res.json(data.map((item) => ({ date: `${item._id.year}-${String(item._id.month).padStart(2, '0')}-${String(item._id.day).padStart(2, '0')}`, count: item.count })));
}

export async function getMonthly(req, res) {
  const data = await ViewLog.aggregate([
    {
      $group: {
        _id: {
          year: { $year: '$createdAt' },
          month: { $month: '$createdAt' }
        },
        count: { $sum: 1 }
      }
    },
    { $sort: { '_id.year': 1, '_id.month': 1 } }
  ]);

  res.json(data.map((item) => ({ month: `${item._id.year}-${String(item._id.month).padStart(2, '0')}`, count: item.count })));
}

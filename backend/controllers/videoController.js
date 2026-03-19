import mongoose from 'mongoose';
import { Video } from '../models/Video.js';
import { Comment } from '../models/Comment.js';
import { ViewLog } from '../models/ViewLog.js';
import { Tag } from '../models/Tag.js';
import { Category } from '../models/Category.js';

function normalizeTags(tags) {
  if (Array.isArray(tags)) return tags.map((tag) => String(tag).trim()).filter(Boolean);
  if (typeof tags === 'string') {
    return tags.split(',').map((tag) => tag.trim()).filter(Boolean);
  }
  return [];
}

async function syncMetadata(category, tags) {
  if (category) {
    await Category.findOneAndUpdate(
      { name: category.trim() },
      { $setOnInsert: { name: category.trim() } },
      { upsert: true, new: true }
    );
  }

  if (tags.length) {
    await Promise.all(
      tags.map((name) =>
        Tag.findOneAndUpdate({ name }, { $setOnInsert: { name } }, { upsert: true, new: true })
      )
    );
  }
}

export async function getVideos(req, res) {
  const { category, tag, q } = req.query;
  const filter = {};

  if (category) filter.category = category;
  if (tag) filter.tags = tag;
  if (q) {
    filter.$or = [
      { title: { $regex: q, $options: 'i' } },
      { tags: { $elemMatch: { $regex: q, $options: 'i' } } }
    ];
  }

  const videos = await Video.find(filter).sort({ createdAt: -1 });
  res.json(videos);
}

export async function getVideoById(req, res) {
  const { id } = req.params;
  if (!mongoose.Types.ObjectId.isValid(id)) {
    return res.status(400).json({ message: 'Invalid video id' });
  }

  const video = await Video.findById(id);
  if (!video) {
    return res.status(404).json({ message: 'Video not found' });
  }

  video.views += 1;
  await video.save();

  await ViewLog.create({
    videoId: video._id,
    ip: req.headers['x-forwarded-for']?.split(',')[0]?.trim() || req.socket.remoteAddress || 'unknown'
  });

  const comments = await Comment.find({ videoId: video._id }).sort({ createdAt: -1 });
  const relatedVideos = await Video.find({
    _id: { $ne: video._id },
    $or: [{ category: video.category }, { tags: { $in: video.tags } }]
  })
    .limit(8)
    .sort({ views: -1, createdAt: -1 });

  return res.json({ ...video.toObject(), comments, relatedVideos });
}

export async function createVideo(req, res) {
  const payload = req.body;
  const tags = normalizeTags(payload.tags);
  const video = await Video.create({ ...payload, tags });
  await syncMetadata(video.category, tags);
  res.status(201).json(video);
}

export async function updateVideo(req, res) {
  const { id } = req.params;
  const payload = req.body;
  const tags = normalizeTags(payload.tags);
  const video = await Video.findByIdAndUpdate(id, { ...payload, tags }, { new: true, runValidators: true });
  if (!video) return res.status(404).json({ message: 'Video not found' });
  await syncMetadata(video.category, tags);
  res.json(video);
}

export async function deleteVideo(req, res) {
  const { id } = req.params;
  const video = await Video.findByIdAndDelete(id);
  if (!video) return res.status(404).json({ message: 'Video not found' });
  await Comment.deleteMany({ videoId: id });
  await ViewLog.deleteMany({ videoId: id });
  res.json({ message: 'Video deleted' });
}

export async function searchVideos(req, res) {
  const q = req.query.q || '';
  const videos = await Video.find({
    $or: [
      { title: { $regex: q, $options: 'i' } },
      { tags: { $elemMatch: { $regex: q, $options: 'i' } } }
    ]
  }).sort({ createdAt: -1 });

  res.json(videos);
}

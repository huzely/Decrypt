import { Comment } from '../models/Comment.js';

export async function createComment(req, res) {
  const { videoId, text } = req.body;
  if (!videoId || !text?.trim()) {
    return res.status(400).json({ message: 'videoId and text are required' });
  }

  const comment = await Comment.create({ videoId, text: text.trim() });
  res.status(201).json(comment);
}

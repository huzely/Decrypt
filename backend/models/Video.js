import mongoose from 'mongoose';

const videoSchema = new mongoose.Schema(
  {
    title: { type: String, required: true, trim: true },
    description: { type: String, default: '' },
    thumbnail: { type: String, required: true },
    videoUrl: { type: String, required: true },
    type: { type: String, enum: ['mp4', 'embed'], required: true },
    views: { type: Number, default: 0 },
    tags: [{ type: String, trim: true }],
    category: { type: String, required: true, trim: true }
  },
  { timestamps: true }
);

export const Video = mongoose.model('Video', videoSchema);

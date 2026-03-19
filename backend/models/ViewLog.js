import mongoose from 'mongoose';

const viewLogSchema = new mongoose.Schema(
  {
    videoId: { type: mongoose.Schema.Types.ObjectId, ref: 'Video', required: true },
    ip: { type: String, default: 'unknown' }
  },
  { timestamps: true }
);

export const ViewLog = mongoose.model('ViewLog', viewLogSchema);

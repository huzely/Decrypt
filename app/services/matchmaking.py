from __future__ import annotations

from datetime import datetime, timedelta, timezone

from sqlalchemy import and_, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import settings
from app.core.redis_client import redis
from app.models.models import AccountStatus, BlockedPair, ChatRoom, RoomStatus, User


class MatchmakingService:
    queue_key = "matchmaking:queue"
    premium_queue_key = "matchmaking:queue:premium"

    @staticmethod
    async def enqueue(user: User) -> None:
        queue = MatchmakingService.premium_queue_key if user.premium and settings.premium_matchmaking_boost else MatchmakingService.queue_key
        await redis.lrem(queue, 0, str(user.id))
        await redis.rpush(queue, str(user.id))

    @staticmethod
    async def dequeue(user: User) -> None:
        await redis.lrem(MatchmakingService.queue_key, 0, str(user.id))
        await redis.lrem(MatchmakingService.premium_queue_key, 0, str(user.id))

    @staticmethod
    async def try_match(db: AsyncSession, requester: User) -> ChatRoom | None:
        active_room = await db.scalar(
            select(ChatRoom).where(
                and_(
                    ChatRoom.status == RoomStatus.active,
                    or_(ChatRoom.user_a_id == requester.id, ChatRoom.user_b_id == requester.id),
                )
            )
        )
        if active_room:
            return active_room

        for queue in [MatchmakingService.premium_queue_key, MatchmakingService.queue_key]:
            size = await redis.llen(queue)
            for _ in range(size):
                raw_id = await redis.lpop(queue)
                if not raw_id:
                    continue
                candidate_id = int(raw_id)
                if candidate_id == requester.id:
                    continue
                candidate = await db.get(User, candidate_id)
                if not candidate or candidate.status != AccountStatus.active:
                    continue
                if candidate.trust_score < settings.matchmaking_min_trust_score:
                    continue
                if not MatchmakingService._gender_compatible(requester, candidate):
                    continue
                if await MatchmakingService._is_blocked(db, requester.id, candidate.id):
                    continue

                expires_minutes = settings.premium_session_minutes if requester.premium or candidate.premium else settings.base_session_minutes
                room = ChatRoom(
                    user_a_id=requester.id,
                    user_b_id=candidate.id,
                    status=RoomStatus.active,
                    expires_at=datetime.now(timezone.utc) + timedelta(minutes=expires_minutes),
                )
                db.add(room)
                await db.commit()
                await db.refresh(room)
                return room
        return None

    @staticmethod
    def _gender_compatible(a: User, b: User) -> bool:
        return a.preferred_match_gender == b.gender and b.preferred_match_gender == a.gender

    @staticmethod
    async def _is_blocked(db: AsyncSession, a_id: int, b_id: int) -> bool:
        low, high = sorted([a_id, b_id])
        blocked = await db.scalar(select(BlockedPair).where(BlockedPair.user_low_id == low, BlockedPair.user_high_id == high))
        return blocked is not None

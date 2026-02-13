from datetime import datetime, timezone

from sqlalchemy import and_, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models.models import AccountStatus, ChatRoom, RoomStatus, User
from app.services.trust import TrustService


class MaintenanceService:
    @staticmethod
    async def close_expired_rooms(db: AsyncSession) -> int:
        now = datetime.now(timezone.utc)
        rooms = (
            await db.execute(
                select(ChatRoom).where(and_(ChatRoom.status == RoomStatus.active, ChatRoom.expires_at < now))
            )
        ).scalars().all()
        for room in rooms:
            room.status = RoomStatus.closed
            room.closed_at = now
        await db.commit()
        return len(rooms)

    @staticmethod
    async def release_user_restrictions(db: AsyncSession) -> int:
        now = datetime.now(timezone.utc)
        users = (
            await db.execute(
                select(User).where(
                    or_(
                        and_(User.status == AccountStatus.cooldown, User.cooldown_until < now),
                        and_(User.status == AccountStatus.banned, User.suspended_until < now),
                    )
                )
            )
        ).scalars().all()
        for user in users:
            user.status = AccountStatus.active
            user.cooldown_until = None
            user.suspended_until = None
            await TrustService.try_regenerate(user, db)
        await db.commit()
        return len(users)

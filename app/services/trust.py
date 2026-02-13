from datetime import datetime, timedelta, timezone

from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import settings
from app.models.models import AccountStatus, User


class TrustService:
    @staticmethod
    async def apply_report_penalty(db: AsyncSession, user: User) -> None:
        user.trust_score = max(0, user.trust_score - settings.trust_penalty_on_report)
        user.last_report_at = datetime.now(timezone.utc)
        if user.trust_score < settings.trust_suspend_threshold:
            user.status = AccountStatus.banned
            user.suspended_until = datetime.now(timezone.utc) + timedelta(days=7)
        elif user.trust_score < settings.trust_cooldown_threshold:
            user.status = AccountStatus.cooldown
            user.cooldown_until = datetime.now(timezone.utc) + timedelta(hours=24)
        await db.commit()

    @staticmethod
    async def try_regenerate(user: User, db: AsyncSession) -> None:
        if not user.last_report_at:
            return
        delta = datetime.now(timezone.utc) - user.last_report_at.replace(tzinfo=timezone.utc)
        if delta.days >= settings.trust_regen_days:
            user.trust_score = min(100, user.trust_score + settings.trust_regen_amount)
            await db.commit()

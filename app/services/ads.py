from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models.models import Ad


class AdsService:
    @staticmethod
    async def next_ad(db: AsyncSession) -> Ad | None:
        ad = await db.scalar(select(Ad).where(Ad.active.is_(True)).order_by(Ad.impressions.asc(), Ad.id.asc()))
        if ad:
            ad.impressions += 1
            await db.commit()
        return ad

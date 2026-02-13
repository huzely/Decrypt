from datetime import date

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy import case, func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.deps import require_admin
from app.db.session import get_db
from app.models.models import (
    AccountStatus,
    Ad,
    BlockedPair,
    ChatRoom,
    DailyVerificationToken,
    Report,
    User,
)

router = APIRouter(prefix="/admin", tags=["admin"], dependencies=[Depends(require_admin)])


@router.get("/stats/users")
async def user_stats(db: AsyncSession = Depends(get_db)):
    total = await db.scalar(select(func.count(User.id)))
    premium = await db.scalar(select(func.count(User.id)).where(User.premium.is_(True)))
    active = await db.scalar(select(func.count(User.id)).where(User.status == AccountStatus.active))
    return {"total": total, "premium": premium, "active": active}


@router.get("/stats/chats")
async def chat_stats(db: AsyncSession = Depends(get_db)):
    total_rooms = await db.scalar(select(func.count(ChatRoom.id)))
    active_rooms = await db.scalar(select(func.count(ChatRoom.id)).where(ChatRoom.status == "active"))
    reports = await db.scalar(select(func.count(Report.id)))
    return {"rooms_total": total_rooms, "rooms_active": active_rooms, "reports_total": reports}


@router.get("/reports")
async def list_reports(db: AsyncSession = Depends(get_db)):
    rows = (await db.execute(select(Report).order_by(Report.created_at.desc()).limit(100))).scalars().all()
    return [{"id": r.id, "room_id": r.room_id, "reporter_id": r.reporter_id, "reported_id": r.reported_id, "reason": r.reason, "created_at": r.created_at} for r in rows]


@router.get("/trust-distribution")
async def trust_distribution(db: AsyncSession = Depends(get_db)):
    query = select(
        func.count(case((User.trust_score >= 80, 1))).label("high"),
        func.count(case(((User.trust_score >= 40) & (User.trust_score < 80), 1))).label("medium"),
        func.count(case((User.trust_score < 40, 1))).label("low"),
    )
    result = (await db.execute(query)).mappings().one()
    return dict(result)


@router.get("/blocked-pairs")
async def blocked_pairs(db: AsyncSession = Depends(get_db)):
    rows = (await db.execute(select(BlockedPair))).scalars().all()
    return [{"id": b.id, "user_low_id": b.user_low_id, "user_high_id": b.user_high_id, "created_at": b.created_at} for b in rows]


@router.post("/daily-token")
async def set_daily_token(token: str, db: AsyncSession = Depends(get_db)):
    today = date.today()
    existing = await db.scalar(select(DailyVerificationToken).where(DailyVerificationToken.effective_date == today))
    if existing:
        existing.token = token
    else:
        db.add(DailyVerificationToken(token=token, effective_date=today))
    await db.commit()
    return {"message": "Daily verification token set", "date": str(today)}


@router.get("/ads")
async def list_ads(db: AsyncSession = Depends(get_db)):
    rows = (await db.execute(select(Ad).order_by(Ad.created_at.desc()))).scalars().all()
    return [{"id": a.id, "title": a.title, "payload": a.payload, "active": a.active, "impressions": a.impressions, "created_at": a.created_at} for a in rows]


@router.post("/ads")
async def create_ad(title: str, payload: str, active: bool = True, db: AsyncSession = Depends(get_db)):
    ad = Ad(title=title, payload=payload, active=active)
    db.add(ad)
    await db.commit()
    await db.refresh(ad)
    return {"id": ad.id, "title": ad.title, "payload": ad.payload, "active": ad.active, "impressions": ad.impressions}


@router.patch("/ads/{ad_id}")
async def update_ad(ad_id: int, title: str | None = None, payload: str | None = None, active: bool | None = None, db: AsyncSession = Depends(get_db)):
    ad = await db.get(Ad, ad_id)
    if not ad:
        raise HTTPException(status_code=404, detail="Ad not found")
    if title is not None:
        ad.title = title
    if payload is not None:
        ad.payload = payload
    if active is not None:
        ad.active = active
    await db.commit()
    return {"id": ad.id, "title": ad.title, "payload": ad.payload, "active": ad.active, "impressions": ad.impressions}


@router.delete("/ads/{ad_id}")
async def delete_ad(ad_id: int, db: AsyncSession = Depends(get_db)):
    ad = await db.get(Ad, ad_id)
    if not ad:
        raise HTTPException(status_code=404, detail="Ad not found")
    await db.delete(ad)
    await db.commit()
    return {"message": "Ad deleted"}

from datetime import datetime, timezone

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy import and_, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import settings
from app.core.deps import ensure_verified_today, get_current_user, rate_limit
from app.db.session import get_db
from app.models.models import AccountStatus, BlockedPair, ChatRoom, Message, Report, RoomStatus, User
from app.schemas.common import MatchResponse, ReportRequest
from app.services.ads import AdsService
from app.services.matchmaking import MatchmakingService
from app.services.trust import TrustService


router = APIRouter(prefix="/match", tags=["matchmaking"], dependencies=[Depends(rate_limit)])


@router.post("/find", response_model=MatchResponse | dict)
async def find_match(user: User = Depends(ensure_verified_today), db: AsyncSession = Depends(get_db)):
    if user.status != AccountStatus.active:
        raise HTTPException(status_code=403, detail="Account not active")
    if user.trust_score < settings.matchmaking_min_trust_score:
        raise HTTPException(status_code=403, detail="Trust score too low")

    existing_room = await db.scalar(
        select(ChatRoom).where(
            and_(
                ChatRoom.status == RoomStatus.active,
                ChatRoom.expires_at > datetime.now(timezone.utc),
                or_(ChatRoom.user_a_id == user.id, ChatRoom.user_b_id == user.id),
            )
        )
    )
    if existing_room:
        return MatchResponse(room_id=existing_room.id, expires_at=existing_room.expires_at)

    room = await MatchmakingService.try_match(db, user)
    if room:
        return MatchResponse(room_id=room.id, expires_at=room.expires_at)

    await MatchmakingService.enqueue(user)
    return {"message": "Queued for matchmaking"}


@router.post("/cancel")
async def cancel_matchmaking(user: User = Depends(get_current_user)):
    await MatchmakingService.dequeue(user)
    return {"message": "Removed from queue"}


@router.post("/rooms/{room_id}/report")
async def report_user(room_id: int, payload: ReportRequest, user: User = Depends(get_current_user), db: AsyncSession = Depends(get_db)):
    room = await db.get(ChatRoom, room_id)
    if not room or room.status != RoomStatus.active:
        raise HTTPException(status_code=404, detail="Room not found")
    if user.id not in [room.user_a_id, room.user_b_id]:
        raise HTTPException(status_code=403, detail="Not your room")

    target_id = room.user_b_id if user.id == room.user_a_id else room.user_a_id
    db.add(Report(room_id=room_id, reporter_id=user.id, reported_id=target_id, reason=payload.reason))

    low, high = sorted([user.id, target_id])
    exists = await db.scalar(select(BlockedPair).where(BlockedPair.user_low_id == low, BlockedPair.user_high_id == high))
    if not exists:
        db.add(BlockedPair(user_low_id=low, user_high_id=high))

    target = await db.get(User, target_id)
    await db.commit()
    await TrustService.apply_report_penalty(db, target)

    return {"message": "User reported"}


@router.post("/rooms/{room_id}/close")
async def close_room(room_id: int, user: User = Depends(get_current_user), db: AsyncSession = Depends(get_db)):
    room = await db.get(ChatRoom, room_id)
    if not room:
        raise HTTPException(status_code=404, detail="Room not found")
    if user.id not in [room.user_a_id, room.user_b_id]:
        raise HTTPException(status_code=403, detail="Not your room")

    room.status = RoomStatus.closed
    room.closed_at = datetime.now(timezone.utc)
    await db.commit()

    if not user.premium:
        ad = await AdsService.next_ad(db)
        if ad:
            return {"message": "Room closed", "ad": {"title": ad.title, "payload": ad.payload}}
    return {"message": "Room closed"}

from datetime import date

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.deps import get_current_user, rate_limit
from app.db.session import get_db
from app.models.models import DailyVerificationToken, User, UserVerification
from app.schemas.common import PremiumActivateRequest, UserOut, VerifyRequest


router = APIRouter(prefix="/user", tags=["user"], dependencies=[Depends(rate_limit)])


@router.get("/me", response_model=UserOut)
async def me(user: User = Depends(get_current_user)):
    return user


@router.post("/verify")
async def verify(payload: VerifyRequest, user: User = Depends(get_current_user), db: AsyncSession = Depends(get_db)):
    today = date.today()
    token = await db.scalar(select(DailyVerificationToken).where(DailyVerificationToken.effective_date == today))
    if not token or token.token != payload.token:
        raise HTTPException(status_code=400, detail="Invalid daily token")
    existing = await db.scalar(
        select(UserVerification).where(UserVerification.user_id == user.id, UserVerification.verified_date == today)
    )
    if existing:
        return {"message": "Already verified"}
    db.add(UserVerification(user_id=user.id, verified_date=today))
    await db.commit()
    return {"message": "Verified for today"}


@router.post("/premium/activate")
async def activate_premium(
    payload: PremiumActivateRequest,
    user: User = Depends(get_current_user),
    db: AsyncSession = Depends(get_db),
):
    if not payload.payment_reference:
        raise HTTPException(status_code=400, detail="Payment reference required")
    user.premium = True
    await db.commit()
    return {"message": "Premium activated", "premium": True}

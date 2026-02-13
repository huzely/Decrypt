from fastapi import Depends, HTTPException, Request, status
from fastapi.security import HTTPAuthorizationCredentials, HTTPBearer
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import settings
from app.core.redis_client import redis
from app.core.security import decode_token
from app.db.session import get_db
from app.models.models import User


bearer = HTTPBearer(auto_error=True)


async def rate_limit(request: Request):
    ip = request.client.host if request.client else "unknown"
    key = f"ratelimit:{ip}"
    count = await redis.incr(key)
    if count == 1:
        await redis.expire(key, 60)
    if count > settings.rate_limit_per_minute:
        raise HTTPException(status_code=429, detail="Rate limit exceeded")


async def get_current_user(
    credentials: HTTPAuthorizationCredentials = Depends(bearer),
    db: AsyncSession = Depends(get_db),
) -> User:
    user_id = decode_token(credentials.credentials)
    if not user_id:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid token")
    user = await db.get(User, int(user_id))
    if not user:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="User not found")
    return user


async def require_admin(user: User = Depends(get_current_user)) -> User:
    if not user.is_admin:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Admin only")
    return user


async def ensure_verified_today(user: User = Depends(get_current_user), db: AsyncSession = Depends(get_db)) -> User:
    from datetime import date
    from app.models.models import UserVerification

    verified = await db.scalar(
        select(UserVerification).where(
            UserVerification.user_id == user.id,
            UserVerification.verified_date == date.today(),
        )
    )
    if not verified:
        raise HTTPException(status_code=403, detail="Daily verification required")
    return user

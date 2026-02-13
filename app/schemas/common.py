from datetime import datetime

from pydantic import BaseModel, ConfigDict

from app.models.models import AccountStatus


class UserOut(BaseModel):
    model_config = ConfigDict(from_attributes=True)
    id: int
    username: str
    trust_score: int
    premium: bool
    status: AccountStatus


class MatchResponse(BaseModel):
    room_id: int
    expires_at: datetime


class VerifyRequest(BaseModel):
    token: str


class ReportRequest(BaseModel):
    reason: str


class PremiumActivateRequest(BaseModel):
    payment_reference: str

from contextlib import asynccontextmanager

from fastapi import FastAPI

from app.api import admin, auth, match, user, ws
from app.core.redis_client import redis


@asynccontextmanager
async def lifespan(_: FastAPI):
    yield
    await redis.aclose()


app = FastAPI(title="Anonymous Matchmaking Bot API", lifespan=lifespan)

app.include_router(auth.router)
app.include_router(user.router)
app.include_router(match.router)
app.include_router(admin.router)
app.include_router(ws.router)


@app.get("/health")
async def health():
    return {"status": "ok"}

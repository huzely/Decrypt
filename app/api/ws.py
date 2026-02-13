from datetime import datetime, timezone

from fastapi import APIRouter, Depends, WebSocket, WebSocketDisconnect
from jose import JWTError, jwt
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import settings
from app.db.session import AsyncSessionLocal
from app.models.models import ChatRoom, Message, RoomStatus
from app.websocket.manager import manager


router = APIRouter(tags=["websocket"])


async def get_room_and_user(token: str, room_id: int, db: AsyncSession):
    try:
        user_id = int(jwt.decode(token, settings.secret_key, algorithms=[settings.algorithm])["sub"])
    except (JWTError, KeyError, ValueError):
        return None, None
    room = await db.get(ChatRoom, room_id)
    if not room or room.status != RoomStatus.active:
        return None, None
    if datetime.now(timezone.utc) > room.expires_at.replace(tzinfo=timezone.utc):
        room.status = RoomStatus.closed
        room.closed_at = datetime.now(timezone.utc)
        await db.commit()
        return None, None
    if user_id not in [room.user_a_id, room.user_b_id]:
        return None, None
    return room, user_id


@router.websocket("/ws/chat/{room_id}")
async def chat_socket(websocket: WebSocket, room_id: int, token: str):
    async with AsyncSessionLocal() as db:
        room, user_id = await get_room_and_user(token, room_id, db)
        if not room:
            await websocket.close(code=1008)
            return

        await manager.connect(room_id, user_id, websocket)
        await manager.send_to_room(room_id, {"type": "system", "message": "Stranger connected"})

        try:
            while True:
                data = await websocket.receive_json()
                content = str(data.get("message", "")).strip()
                if not content:
                    continue
                msg = Message(room_id=room_id, sender_id=user_id, content=content)
                db.add(msg)
                await db.commit()
                await manager.send_to_other(room_id, user_id, {"type": "message", "from": "Stranger", "message": content})
        except WebSocketDisconnect:
            manager.disconnect(room_id, user_id)

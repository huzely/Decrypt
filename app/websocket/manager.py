from collections import defaultdict

from fastapi import WebSocket


class ConnectionManager:
    def __init__(self):
        self.room_connections: dict[int, dict[int, WebSocket]] = defaultdict(dict)

    async def connect(self, room_id: int, user_id: int, websocket: WebSocket):
        await websocket.accept()
        self.room_connections[room_id][user_id] = websocket

    def disconnect(self, room_id: int, user_id: int):
        self.room_connections.get(room_id, {}).pop(user_id, None)

    async def send_to_other(self, room_id: int, sender_id: int, payload: dict):
        for uid, ws in self.room_connections.get(room_id, {}).items():
            if uid != sender_id:
                await ws.send_json(payload)

    async def send_to_room(self, room_id: int, payload: dict):
        for ws in self.room_connections.get(room_id, {}).values():
            await ws.send_json(payload)


manager = ConnectionManager()

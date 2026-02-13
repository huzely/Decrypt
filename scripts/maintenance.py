import asyncio

from app.db.session import AsyncSessionLocal
from app.services.maintenance import MaintenanceService


async def run():
    async with AsyncSessionLocal() as db:
        closed = await MaintenanceService.close_expired_rooms(db)
        restored = await MaintenanceService.release_user_restrictions(db)
        print({"closed_rooms": closed, "restored_users": restored})


if __name__ == "__main__":
    asyncio.run(run())

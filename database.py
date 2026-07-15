import asyncpg
import config

_pool: asyncpg.Pool | None = None


async def get_pool() -> asyncpg.Pool:
    global _pool
    if _pool is None:
        _pool = await asyncpg.create_pool(config.DATABASE_URL, min_size=2, max_size=10)
    return _pool


async def execute(query: str, *args):
    pool = await get_pool()
    return await pool.fetch(query, *args)


async def execute_one(query: str, *args):
    pool = await get_pool()
    return await pool.fetchrow(query, *args)


async def execute_insert(query: str, *args):
    pool = await get_pool()
    return await pool.fetchval(query, *args)

from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    app_name: str = "Anonymous Matchmaking"
    env: str = "dev"
    secret_key: str = "change-me"
    algorithm: str = "HS256"
    access_token_expire_minutes: int = 60 * 24

    database_url: str = "postgresql+asyncpg://postgres:postgres@db:5432/matchbot"
    redis_url: str = "redis://redis:6379/0"

    matchmaking_min_trust_score: int = 40
    trust_cooldown_threshold: int = 40
    trust_suspend_threshold: int = 20
    trust_penalty_on_report: int = 20
    trust_regen_days: int = 3
    trust_regen_amount: int = 5

    base_session_minutes: int = 15
    premium_session_minutes: int = 30
    premium_matchmaking_boost: bool = True

    rate_limit_per_minute: int = 60

    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8")


settings = Settings()

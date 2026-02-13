"""initial schema

Revision ID: 0001_initial
Revises: 
Create Date: 2026-02-13 00:00:00
"""

from alembic import op
import sqlalchemy as sa


revision = "0001_initial"
down_revision = None
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "users",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("username", sa.String(length=64), nullable=False, unique=True),
        sa.Column("password_hash", sa.String(length=255), nullable=False),
        sa.Column("is_admin", sa.Boolean(), nullable=False, server_default=sa.text("false")),
        sa.Column("gender", sa.Enum("male", "female", name="gender"), nullable=False),
        sa.Column("preferred_match_gender", sa.Enum("male", "female", name="gender"), nullable=False),
        sa.Column("trust_score", sa.Integer(), nullable=False, server_default="100"),
        sa.Column("premium", sa.Boolean(), nullable=False, server_default=sa.text("false")),
        sa.Column("status", sa.Enum("active", "cooldown", "banned", name="accountstatus"), nullable=False, server_default="active"),
        sa.Column("cooldown_until", sa.DateTime(timezone=True), nullable=True),
        sa.Column("suspended_until", sa.DateTime(timezone=True), nullable=True),
        sa.Column("last_report_at", sa.DateTime(timezone=True), nullable=True),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
    )
    op.create_index("ix_users_id", "users", ["id"])

    op.create_table(
        "chat_rooms",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("user_a_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("user_b_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("status", sa.Enum("active", "closed", name="roomstatus"), nullable=False, server_default="active"),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
        sa.Column("expires_at", sa.DateTime(timezone=True), nullable=False),
        sa.Column("closed_at", sa.DateTime(timezone=True), nullable=True),
    )

    op.create_table(
        "messages",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("room_id", sa.Integer(), sa.ForeignKey("chat_rooms.id"), nullable=False),
        sa.Column("sender_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("content", sa.Text(), nullable=False),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
    )
    op.create_index("ix_messages_room_id", "messages", ["room_id"])

    op.create_table(
        "reports",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("room_id", sa.Integer(), sa.ForeignKey("chat_rooms.id"), nullable=False),
        sa.Column("reporter_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("reported_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("reason", sa.String(length=255), nullable=False),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
    )

    op.create_table(
        "blocked_pairs",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("user_low_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("user_high_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
        sa.UniqueConstraint("user_low_id", "user_high_id", name="uq_blocked_pair"),
    )

    op.create_table(
        "ads",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("title", sa.String(length=120), nullable=False),
        sa.Column("payload", sa.Text(), nullable=False),
        sa.Column("active", sa.Boolean(), nullable=False, server_default=sa.text("true")),
        sa.Column("impressions", sa.Integer(), nullable=False, server_default="0"),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
    )

    op.create_table(
        "daily_verification_tokens",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("token", sa.String(length=64), nullable=False),
        sa.Column("effective_date", sa.Date(), nullable=False, unique=True),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
    )

    op.create_table(
        "user_verifications",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("user_id", sa.Integer(), sa.ForeignKey("users.id"), nullable=False),
        sa.Column("verified_date", sa.Date(), nullable=False),
        sa.Column("created_at", sa.DateTime(timezone=True), nullable=False),
        sa.UniqueConstraint("user_id", "verified_date", name="uq_user_verified_daily"),
    )


def downgrade() -> None:
    op.drop_table("user_verifications")
    op.drop_table("daily_verification_tokens")
    op.drop_table("ads")
    op.drop_table("blocked_pairs")
    op.drop_table("reports")
    op.drop_index("ix_messages_room_id", table_name="messages")
    op.drop_table("messages")
    op.drop_table("chat_rooms")
    op.drop_index("ix_users_id", table_name="users")
    op.drop_table("users")

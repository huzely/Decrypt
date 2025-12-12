import os
from flask import Flask, request, jsonify
import pymysql
import requests

app = Flask(__name__)

DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_NAME = os.getenv('DB_NAME', 'decrypt_db')
DB_USER = os.getenv('DB_USER', 'db_user')
DB_PASS = os.getenv('DB_PASS', 'db_pass')
TELEGRAM_BOT_TOKEN = os.getenv('TELEGRAM_BOT_TOKEN', 'YOUR_TELEGRAM_BOT_TOKEN')
BASE_URL = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}"


def db_conn():
    return pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME, charset='utf8mb4', cursorclass=pymysql.cursors.DictCursor)


def send_message(chat_id, text, keyboard=None):
    payload = {"chat_id": chat_id, "text": text, "parse_mode": "HTML"}
    if keyboard:
        payload["reply_markup"] = {"keyboard": keyboard, "resize_keyboard": True}
    requests.post(f"{BASE_URL}/sendMessage", json=payload, timeout=10)


def is_admin(chat_id):
    with db_conn() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT 1 FROM telegram_admins WHERE telegram_chat_id=%s AND is_active=1", (chat_id,))
            return cur.fetchone() is not None


def admin_menu():
    return [["/stats_today", "/stats_total"], ["/top_posts"], ["/publish", "/unpublish"], ["/setingadmin", "/help"]]


def fetch_post(identifier):
    with db_conn() as conn:
        with conn.cursor() as cur:
            if str(identifier).isdigit():
                cur.execute("SELECT * FROM posts WHERE id=%s", (int(identifier),))
            else:
                cur.execute("SELECT * FROM posts WHERE slug=%s", (identifier,))
            return cur.fetchone()


def stats_summary(today_only=False):
    with db_conn() as conn:
        with conn.cursor() as cur:
            if today_only:
                cur.execute("SELECT COALESCE(SUM(pageviews),0) pv, COALESCE(SUM(unique_visitors),0) uv FROM page_views WHERE visit_date=CURDATE()")
                pv, uv = cur.fetchone().values()
                cur.execute("SELECT COALESCE(SUM(is_valid),0) FROM click_logs WHERE DATE(clicked_at)=CURDATE()")
                clicks = list(cur.fetchone().values())[0]
            else:
                cur.execute("SELECT COALESCE(SUM(pageviews),0) pv, COALESCE(SUM(unique_visitors),0) uv FROM page_views")
                pv, uv = cur.fetchone().values()
                cur.execute("SELECT COALESCE(SUM(is_valid),0) FROM click_logs")
                clicks = list(cur.fetchone().values())[0]
            return pv, uv, clicks


def top_posts():
    with db_conn() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT id, slug, title, shopee_click_count FROM posts ORDER BY shopee_click_count DESC LIMIT 5")
            return cur.fetchall()


def set_status(identifier, status):
    with db_conn() as conn:
        with conn.cursor() as cur:
            post = fetch_post(identifier)
            if not post:
                return None
            cur.execute("UPDATE posts SET status=%s WHERE id=%s", (status, post['id']))
            conn.commit()
            return post


@app.post('/webhook')
def webhook():
    update = request.get_json(force=True)
    message = update.get('message') or update.get('edited_message')
    if not message:
        return jsonify({'ok': True})
    chat_id = message['chat']['id']
    text = message.get('text', '').strip()

    if not is_admin(chat_id):
        send_message(chat_id, "Bạn không có quyền sử dụng bot này")
        return jsonify({'ok': True})

    if text in ['/start', '/help']:
        send_message(chat_id, "Bot quản trị tin tức. Dùng /menu để xem chức năng.")
    if text in ['/menu']:
        send_message(chat_id, "Menu quản trị", keyboard=admin_menu())
    elif text in ['/stats_today']:
        pv, uv, clicks = stats_summary(today_only=True)
        send_message(chat_id, f"Hôm nay\nPV: {pv}\nUnique: {uv}\nShopee hợp lệ: {clicks}")
    elif text in ['/stats_total']:
        pv, uv, clicks = stats_summary(False)
        send_message(chat_id, f"Tổng\nPV: {pv}\nUnique: {uv}\nShopee hợp lệ: {clicks}")
    elif text.startswith('/top_posts'):
        items = top_posts()
        lines = [f"#{p['id']} {p['title']} ({p['shopee_click_count']} click)" for p in items]
        send_message(chat_id, "Top 5:\n" + "\n".join(lines))
    elif text.startswith('/post'):
        parts = text.split(maxsplit=1)
        if len(parts) == 2:
            post = fetch_post(parts[1])
            if post:
                send_message(chat_id, f"#{post['id']} {post['title']}\nStatus: {post['status']}\nSlug: {post['slug']}")
            else:
                send_message(chat_id, 'Không tìm thấy bài')
    elif text.startswith('/publish'):
        parts = text.split(maxsplit=1)
        if len(parts) == 2:
            post = set_status(parts[1], 'published')
            if post:
                send_message(chat_id, f"Đã publish #{post['id']} {post['title']}")
            else:
                send_message(chat_id, 'Không tìm thấy bài')
    elif text.startswith('/unpublish'):
        parts = text.split(maxsplit=1)
        if len(parts) == 2:
            post = set_status(parts[1], 'draft')
            if post:
                send_message(chat_id, f"Đã chuyển draft #{post['id']} {post['title']}")
            else:
                send_message(chat_id, 'Không tìm thấy bài')
    elif text.startswith('/setingadmin'):
        with db_conn() as conn:
            with conn.cursor() as cur:
                cur.execute("SELECT telegram_chat_id,name FROM telegram_admins WHERE is_active=1")
                admins = cur.fetchall()
                lines = [f"{a['name']} ({a['telegram_chat_id']})" for a in admins]
                send_message(chat_id, "Admin đang bật:\n" + "\n".join(lines))
    return jsonify({'ok': True})


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=int(os.getenv('PORT', 8080)))

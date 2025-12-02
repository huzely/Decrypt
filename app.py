import os
import uuid
from datetime import datetime
from functools import wraps

from flask import (
    Flask,
    flash,
    redirect,
    render_template,
    request,
    send_from_directory,
    session,
    url_for,
)
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import func
from werkzeug.security import check_password_hash, generate_password_hash
from werkzeug.utils import secure_filename


def create_app():
    app = Flask(__name__)
    app.config["SECRET_KEY"] = os.environ.get("SECRET_KEY", "change-me")
    app.config["SQLALCHEMY_DATABASE_URI"] = "sqlite:///site.db"
    app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False
    upload_dir = os.path.join(app.root_path, "static", "uploads")
    os.makedirs(upload_dir, exist_ok=True)
    app.config["UPLOAD_FOLDER"] = upload_dir

    return app


app = create_app()
db = SQLAlchemy(app)


class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    password_hash = db.Column(db.String(255), nullable=False)

    def set_password(self, password: str) -> None:
        self.password_hash = generate_password_hash(password)

    def check_password(self, password: str) -> bool:
        return check_password_hash(self.password_hash, password)


class Link(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    slug = db.Column(db.String(64), unique=True, nullable=False)
    shopee_url = db.Column(db.String(255), nullable=False)
    telegram_url = db.Column(db.String(255), nullable=False)
    target_url = db.Column(db.String(255), nullable=True)
    meta_title = db.Column(db.String(150), nullable=True)
    meta_description = db.Column(db.String(255), nullable=True)
    meta_image = db.Column(db.String(255), nullable=True)
    body_content = db.Column(db.Text, nullable=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)


class Click(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    link_id = db.Column(db.Integer, db.ForeignKey("link.id"), nullable=False)
    ip_address = db.Column(db.String(64), nullable=False)
    user_agent = db.Column(db.String(255), nullable=True)
    browser = db.Column(db.String(64), nullable=True)
    os_name = db.Column(db.String(64), nullable=True)
    visit_number = db.Column(db.Integer, default=1)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    link = db.relationship("Link", backref=db.backref("clicks", lazy=True))


class SiteSettings(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    hero_title = db.Column(db.String(150), default="Link Wrapper")
    hero_subtitle = db.Column(db.String(255), default="Quản lý và theo dõi link bọc Shopee -> Telegram")
    main_color = db.Column(db.String(30), default="#0f172a")
    accent_color = db.Column(db.String(30), default="#22c55e")


TELEGRAM_BOT_TOKEN = os.environ.get("TELEGRAM_BOT_TOKEN")
TELEGRAM_CHAT_ID = os.environ.get("TELEGRAM_CHAT_ID")


def login_required(view_func):
    @wraps(view_func)
    def wrapper(*args, **kwargs):
        if not session.get("user_id"):
            flash("Vui lòng đăng nhập để tiếp tục", "warning")
            return redirect(url_for("login"))
        return view_func(*args, **kwargs)

    return wrapper


def ensure_settings() -> SiteSettings:
    settings = SiteSettings.query.first()
    if not settings:
        settings = SiteSettings()
        db.session.add(settings)
        db.session.commit()
    return settings


@app.context_processor
def inject_settings():
    return {"settings": SiteSettings.query.first()}


def parse_user_agent(user_agent: str) -> tuple[str, str]:
    agent = user_agent.lower() if user_agent else ""
    browser = "Khác"
    if "chrome" in agent:
        browser = "Chrome"
    elif "firefox" in agent:
        browser = "Firefox"
    elif "safari" in agent and "chrome" not in agent:
        browser = "Safari"
    elif "edge" in agent:
        browser = "Edge"

    os_name = "Khác"
    if "windows" in agent:
        os_name = "Windows"
    elif "mac" in agent:
        os_name = "macOS"
    elif "linux" in agent:
        os_name = "Linux"
    elif "android" in agent:
        os_name = "Android"
    elif "iphone" in agent or "ios" in agent:
        os_name = "iOS"

    return browser, os_name


def send_telegram_message(text: str) -> None:
    if not TELEGRAM_BOT_TOKEN or not TELEGRAM_CHAT_ID:
        app.logger.info("Telegram skipped: %s", text)
        return

    import requests

    url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
    payload = {"chat_id": TELEGRAM_CHAT_ID, "text": text}
    try:
        requests.post(url, json=payload, timeout=8)
    except Exception as exc:  # noqa: BLE001
        app.logger.warning("Không gửi được telegram: %s", exc)


@app.before_request
def create_tables() -> None:
    db.create_all()
    ensure_settings()


@app.route("/")
def index():
    settings = ensure_settings()
    links = Link.query.order_by(Link.created_at.desc()).all()
    total_clicks = db.session.query(func.count(Click.id)).scalar() or 0
    return render_template("index.html", settings=settings, links=links, total_clicks=total_clicks)


@app.route("/l/<slug>")
def follow(slug: str):
    link = Link.query.filter_by(slug=slug).first_or_404()
    ip_address = request.headers.get("X-Forwarded-For", request.remote_addr) or "Unknown"
    user_agent = request.headers.get("User-Agent", "")
    browser, os_name = parse_user_agent(user_agent)

    visit_number = (
        Click.query.filter_by(link_id=link.id, ip_address=ip_address).count() + 1
    )

    click = Click(
        link=link,
        ip_address=ip_address,
        user_agent=user_agent,
        browser=browser,
        os_name=os_name,
        visit_number=visit_number,
    )
    db.session.add(click)
    db.session.commit()

    message = (
        f"Click mới cho link {link.slug}: IP {ip_address} (lần {visit_number}), "
        f"Browser: {browser}, OS: {os_name}"
    )
    send_telegram_message(message)

    return render_template(
        "redirect.html",
        link=link,
        meta_title=link.meta_title,
        meta_description=link.meta_description,
        meta_image=link.meta_image,
        title=link.meta_title or "Đang chuyển hướng",
    )


@app.route("/uploads/<path:filename>")
def uploads(filename: str):
    return send_from_directory(app.config["UPLOAD_FOLDER"], filename)


@app.route("/admin")
@login_required
def admin_dashboard():
    total_links = Link.query.count()
    total_clicks = Click.query.count()

    daily_clicks = (
        db.session.query(func.date(Click.created_at), func.count(Click.id))
        .group_by(func.date(Click.created_at))
        .order_by(func.date(Click.created_at).desc())
        .limit(14)
        .all()
    )
    monthly_clicks = (
        db.session.query(func.strftime("%Y-%m", Click.created_at), func.count(Click.id))
        .group_by(func.strftime("%Y-%m", Click.created_at))
        .order_by(func.strftime("%Y-%m", Click.created_at).desc())
        .limit(6)
        .all()
    )

    latest_clicks = (
        Click.query.order_by(Click.created_at.desc()).limit(10).all()
    )

    return render_template(
        "admin/dashboard.html",
        total_links=total_links,
        total_clicks=total_clicks,
        daily_clicks=daily_clicks,
        monthly_clicks=monthly_clicks,
        latest_clicks=latest_clicks,
    )


@app.route("/admin/links")
@login_required
def admin_links():
    links = Link.query.order_by(Link.created_at.desc()).all()
    return render_template("admin/links.html", links=links)


@app.route("/admin/links/new", methods=["GET", "POST"])
@login_required
def create_link():
    if request.method == "POST":
        slug = request.form.get("slug") or uuid.uuid4().hex[:8]
        if Link.query.filter_by(slug=slug).first():
            flash("Slug đã tồn tại, hãy chọn slug khác", "danger")
            return redirect(url_for("create_link"))

        meta_image = handle_upload(request.files.get("meta_image"))
        link = Link(
            slug=slug,
            shopee_url=request.form.get("shopee_url") or "https://shopee.vn",
            telegram_url=request.form.get("telegram_url") or "https://t.me",
            target_url=request.form.get("target_url"),
            meta_title=request.form.get("meta_title"),
            meta_description=request.form.get("meta_description"),
            meta_image=meta_image,
            body_content=request.form.get("body_content"),
        )
        db.session.add(link)
        db.session.commit()

        send_telegram_message(f"Tạo link mới: {link.slug} -> {link.shopee_url} / {link.telegram_url}")
        flash("Tạo link thành công", "success")
        return redirect(url_for("admin_links"))

    return render_template("admin/link_form.html", link=None)


@app.route("/admin/links/<int:link_id>/edit", methods=["GET", "POST"])
@login_required
def edit_link(link_id: int):
    link = Link.query.get_or_404(link_id)
    if request.method == "POST":
        new_slug = request.form.get("slug") or link.slug
        if new_slug != link.slug and Link.query.filter_by(slug=new_slug).first():
            flash("Slug đã tồn tại", "danger")
            return redirect(url_for("edit_link", link_id=link.id))
        link.slug = new_slug
        link.shopee_url = request.form.get("shopee_url") or link.shopee_url
        link.telegram_url = request.form.get("telegram_url") or link.telegram_url
        link.target_url = request.form.get("target_url")
        link.meta_title = request.form.get("meta_title")
        link.meta_description = request.form.get("meta_description")
        link.body_content = request.form.get("body_content")

        uploaded = handle_upload(request.files.get("meta_image"))
        if uploaded:
            link.meta_image = uploaded

        db.session.commit()
        flash("Cập nhật link thành công", "success")
        return redirect(url_for("admin_links"))

    return render_template("admin/link_form.html", link=link)


@app.route("/admin/settings", methods=["GET", "POST"])
@login_required
def edit_settings():
    settings = ensure_settings()
    if request.method == "POST":
        settings.hero_title = request.form.get("hero_title") or settings.hero_title
        settings.hero_subtitle = request.form.get("hero_subtitle") or settings.hero_subtitle
        settings.main_color = request.form.get("main_color") or settings.main_color
        settings.accent_color = request.form.get("accent_color") or settings.accent_color
        db.session.commit()
        flash("Đã lưu giao diện", "success")
        return redirect(url_for("edit_settings"))

    return render_template("admin/settings.html", settings=settings)


@app.route("/admin/register", methods=["GET", "POST"])
def register():
    if request.method == "POST":
        username = request.form.get("username")
        password = request.form.get("password")
        if not username or not password:
            flash("Thiếu tên đăng nhập hoặc mật khẩu", "danger")
            return redirect(url_for("register"))
        if User.query.filter_by(username=username).first():
            flash("Tên đăng nhập đã tồn tại", "danger")
            return redirect(url_for("register"))
        user = User(username=username)
        user.set_password(password)
        db.session.add(user)
        db.session.commit()
        flash("Đăng kí thành công, hãy đăng nhập", "success")
        return redirect(url_for("login"))
    return render_template("auth/register.html")


@app.route("/admin/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        username = request.form.get("username")
        password = request.form.get("password")
        user = User.query.filter_by(username=username).first()
        if not user or not user.check_password(password):
            flash("Sai thông tin đăng nhập", "danger")
            return redirect(url_for("login"))
        session["user_id"] = user.id
        flash("Đăng nhập thành công", "success")
        return redirect(url_for("admin_dashboard"))
    return render_template("auth/login.html")


@app.route("/admin/logout")
@login_required
def logout():
    session.clear()
    flash("Đã đăng xuất", "info")
    return redirect(url_for("login"))


def handle_upload(file_storage):
    if not file_storage or not file_storage.filename:
        return None
    filename = secure_filename(file_storage.filename)
    unique_name = f"{uuid.uuid4().hex}_{filename}"
    file_path = os.path.join(app.config["UPLOAD_FOLDER"], unique_name)
    file_storage.save(file_path)
    return url_for("uploads", filename=unique_name)


if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)

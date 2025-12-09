FROM php:8.2-cli
WORKDIR /app
COPY . /app
RUN docker-php-ext-install pdo pdo_mysql
CMD ["php", "-S", "0.0.0.0:8080", "telegram_webhook.php"]

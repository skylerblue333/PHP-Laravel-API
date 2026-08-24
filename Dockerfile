FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts

FROM php:8.3-cli-alpine
WORKDIR /app
RUN addgroup -S -g 10001 app \
    && adduser -S -D -H -u 10001 -G app app \
    && mkdir -p /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/bootstrap/cache \
    && chown -R app:app /app
COPY --from=vendor /app/vendor ./vendor
COPY --chown=app:app app ./app
COPY --chown=app:app bootstrap ./bootstrap
COPY --chown=app:app public ./public
COPY --chown=app:app routes ./routes
COPY --chown=app:app src ./src
COPY --chown=app:app artisan composer.json ./
USER 10001:10001
EXPOSE 8080
ENV APP_ENV=production \
    LOG_CHANNEL=stderr
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 CMD php -r '$s=@file_get_contents("http://127.0.0.1:8080/healthz"); exit($s===false?1:0);'
ENTRYPOINT ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/index.php"]

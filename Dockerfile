FROM php:8.3-cli-alpine
WORKDIR /app
COPY src ./src
COPY public ./public
RUN addgroup -S app && adduser -S -G app -u 10001 app \
    && chown -R app:app /app
USER 10001:10001
EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 CMD php -r '$s=@file_get_contents("http://127.0.0.1:8080/healthz"); exit($s===false?1:0);'
ENTRYPOINT ["php", "-S", "0.0.0.0:8080", "-t", "public"]

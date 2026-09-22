FROM php:8.3-apache-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends libfreetype6-dev libjpeg62-turbo-dev libpng-dev libonig-dev libxml2-dev libcurl4-openssl-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 mysqli mbstring gd simplexml curl pcntl \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /app
COPY app /app/app
COPY public /app/public
COPY bin /app/bin
COPY tests /app/tests
RUN find /app -name '*.php' -print0 | xargs -0 -n1 php -l \
    && php /app/tests/unit.php \
    && chmod +x /app/bin/start.sh
ENV PORT=8080 DATA_DIR=/data
RUN printf 'display_errors=Off\nlog_errors=On\nerror_log=/proc/self/fd/2\nexpose_php=Off\nmemory_limit=256M\nmax_execution_time=60\n' > /usr/local/etc/php/conf.d/taha.ini
EXPOSE 8080
CMD ["/app/bin/start.sh"]

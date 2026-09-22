FROM php:8.3-apache-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libonig-dev libxml2-dev libcurl4-openssl-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 mysqli mbstring gd simplexml curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app/source
COPY . /app/source

RUN chmod +x /app/source/bin/start.sh

ENV PORT=8080 DATA_DIR=/data
RUN printf 'display_errors=Off\nlog_errors=On\nerror_log=/proc/self/fd/2\nexpose_php=Off\nmemory_limit=256M\nmax_execution_time=120\nauto_prepend_file=/app/runtime/railway.php\n' > /usr/local/etc/php/conf.d/taha-railway.ini

EXPOSE 8080
CMD ["/app/source/bin/start.sh"]

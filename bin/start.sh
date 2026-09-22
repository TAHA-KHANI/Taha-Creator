#!/usr/bin/env bash
set -euo pipefail
PORT="${PORT:-8080}"
[[ "$PORT" =~ ^[0-9]+$ ]] || exit 1
umask 077
php /app/bin/init.php
sed "s/__PORT__/$PORT/g" /app/bin/apache.conf > /etc/apache2/sites-enabled/000-default.conf
printf 'Listen %s\n' "$PORT" > /etc/apache2/ports.conf
php /app/bin/worker.php &
worker_pid=$!
apache2-foreground &
web_pid=$!
stop_all() { kill -TERM "$worker_pid" "$web_pid" 2>/dev/null || true; wait || true; }
trap stop_all TERM INT EXIT
wait -n "$worker_pid" "$web_pid"

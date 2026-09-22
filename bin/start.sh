#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8080}"
DATA_DIR="${DATA_DIR:-/data}"
[[ "$PORT" =~ ^[0-9]+$ ]] || exit 1

runtime_root="$DATA_DIR/www"
mkdir -p "$runtime_root" /app/runtime

for item in /app/source/*; do
  name="$(basename "$item")"
  if [ "$name" = "BotList" ]; then
    continue
  fi
  cp -a "$item" "$runtime_root/"
done
cp -f /app/source/railway.php /app/runtime/railway.php
cp -f /app/source/apache.conf /tmp/taha-apache.conf

cd "$runtime_root"
php init.php

a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

sed -i "s#__PORT__#$PORT#g; s#__DOCROOT__#$runtime_root#g" /tmp/taha-apache.conf
cp /tmp/taha-apache.conf /etc/apache2/sites-enabled/000-default.conf
printf 'Listen %s\n' "$PORT" > /etc/apache2/ports.conf

exec apache2-foreground

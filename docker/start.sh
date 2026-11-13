#!/bin/sh
set -eu
PORT="${PORT:-10000}"
case "$PORT" in *[!0-9]*|'') echo "Invalid PORT" >&2; exit 1;; esac
mkdir -p /var/www/html/data /var/www/html/uploads
chown -R www-data:www-data /var/www/html/data /var/www/html/uploads
cat > /etc/apache2/ports.conf <<EOF
Listen ${PORT}
EOF
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${PORT}>
  DocumentRoot /var/www/html
  <Directory /var/www/html>
    AllowOverride None
    Require all granted
    DirectoryIndex index.php
    Options -Indexes
  </Directory>
  ErrorLog /proc/self/fd/2
  CustomLog /proc/self/fd/1 combined
</VirtualHost>
EOF
exec apache2-foreground

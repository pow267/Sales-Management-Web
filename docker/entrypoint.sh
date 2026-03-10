#!/bin/sh

# Cấp quyền ghi file
chown -R www-data:www-data /var/www/html/public/assets/images 2>/dev/null || true

exec apache2-foreground
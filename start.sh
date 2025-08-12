set -e

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force --no-interaction

php artisan storage:link

apache2-foreground

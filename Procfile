web: vendor/bin/heroku-php-nginx -C heroku/nginx.conf -F heroku/fpm_custom.conf public
messenger: bin/console messenger:consume-messages --limit=100 --time-limit=3600

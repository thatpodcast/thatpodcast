web: vendor/bin/heroku-php-nginx -C heroku/nginx.conf public
messenger: bin/console messenger:consume-messages amqp --limit=100 --time-limit=3600

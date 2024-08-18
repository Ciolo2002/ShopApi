#!/bin/sh


composer dump-autoload

php artisan cache:clear --no-interaction -vvv
php artisan migrate --force -vvv
php artisan db:seed --force -vvv

apache2-foreground
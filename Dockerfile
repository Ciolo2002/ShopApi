ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-apache
RUN requirements="imagemagick libmagickwand-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libicu-dev libzip-dev zlib1g-dev libonig-dev git curl nano libfontconfig unzip libmcrypt-dev pdftk" \
    && apt-get update && apt-get install -y $requirements

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd intl zip opcache mysqli pdo pdo_mysql exif calendar \
    && docker-php-ext-enable mysqli

RUN a2enmod rewrite && a2enmod headers
RUN a2enmod session && a2enmod session_cookie && a2enmod session_crypto && a2enmod auth_form && a2enmod request
RUN  echo "alias ll='ls -alF'" >> ~/.bashrc

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}
RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data
RUN chown www-data:www-data -R /var/www

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig && \
    php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" && \
    php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm -rf /var/log/lastlog /var/log/faillog \

RUN cd /var/www/html
RUN composer create-project laravel/laravel shop
WORKDIR /var/www/html/shop

COPY ./startup.sh /startup.sh
RUN chmod +x /startup.sh
CMD ["/startup.sh"]
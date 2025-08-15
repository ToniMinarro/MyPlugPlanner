FROM php:8.4-fpm-alpine3.22 AS basic
ENV PROJECT_DIR /var/www/html
ENV PATH $PROJECT_DIR/bin:$PROJECT_DIR/vendor/bin:$PATH
WORKDIR $PROJECT_DIR

RUN apk add --no-cache \
        bash libzip-dev libpng-dev openssl-dev postgresql-dev rabbitmq-c-dev ldb-dev libldap openldap-dev && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
    docker-php-ext-install -j$(nproc) pdo_pgsql pdo_mysql mysqli gd zip ldap opcache && \
    pecl install amqp-1.11.0 && \
    docker-php-ext-enable pdo_pgsql pdo_mysql mysqli amqp ldap && \
    apk del .build-deps && \
    rm -rf /tmp/pear

COPY docker/php/config/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/config/php_dev.ini /usr/local/etc/php/php.ini
COPY docker/php/config/opcache_dev.ini /usr/local/etc/php/opcache.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

FROM basic AS local
RUN apk add --no-cache git linux-headers && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
    pecl install xdebug 3 && \
    docker-php-ext-enable xdebug && \
    apk del .build-deps && \
    rm -rf /usr/share/php7 /tmp/pear

FROM basic AS development
RUN apk add --no-cache git linux-headers
COPY ./ $PROJECT_DIR
COPY public.pem /var/www/html/config/jwt/public.pem
COPY private.pem /var/www/html/config/jwt/private.pem
RUN mkdir -p /var/www/html/var
RUN chown -R www-data:www-data $PROJECT_DIR && \
    curl -LO https://github.com/DataDog/dd-trace-php/releases/download/1.5.1/datadog-setup.php && \
    php datadog-setup.php --php-bin=all

USER www-data
RUN composer install --optimize-autoloader --no-scripts

FROM local AS test
ENV APP_ENV=dev
COPY ./ $PROJECT_DIR
RUN mkdir -p /var/www/html/var
USER www-data
RUN composer install --optimize-autoloader --no-scripts

FROM basic AS production
COPY docker/php/config/php_prod.ini /usr/local/etc/php/php.ini
RUN curl -LO https://github.com/DataDog/dd-trace-php/releases/download/1.5.1/datadog-setup.php && \
    php datadog-setup.php --php-bin=all

ENV APP_ENV=prod
COPY ./ $PROJECT_DIR
COPY public.pem /var/www/html/config/jwt/public.pem
COPY private.pem /var/www/html/config/jwt/private.pem
RUN mkdir -p /var/www/html/var
RUN chown -R www-data:www-data $PROJECT_DIR && \
    rm -rf docker tests .env.test grumphp.yml phpcs.xml.dist phpstan.neon phpunit.xml.dist

USER www-data
RUN composer install --no-dev --optimize-autoloader --no-scripts
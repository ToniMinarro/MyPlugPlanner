FROM nginx:1.27-alpine AS base
ADD ./public /var/www/html/public
RUN adduser -u 1000 -D -S -G www-data www-data
RUN chown -R www-data:www-data /var/www/html

FROM base AS local-development
COPY docker/nginx/config/local.conf /etc/nginx/conf.d/default.conf

FROM base AS production
COPY docker/nginx/config/app.conf /etc/nginx/conf.d/default.conf

FROM base AS test
COPY docker/nginx/config/app.conf /etc/nginx/conf.d/default.conf
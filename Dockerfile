FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork rewrite

COPY . /var/www/html/

EXPOSE 80
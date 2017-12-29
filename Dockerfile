# Dockerfile

FROM php:7.0-apache

MAINTAINER me@nalbam.com

COPY src/main/webapp/ /var/www/html/

RUN cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN cd /var/www/html && php /usr/local/bin/composer install

EXPOSE 80

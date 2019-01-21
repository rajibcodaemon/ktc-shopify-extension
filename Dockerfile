FROM php:7-fpm
MAINTAINER Brian Czapiga <bczapiga@beenverified.com>

COPY ./drone/www.conf /usr/local/etc/php-fpm.d/
COPY ./drone/zz-docker.conf /usr/local/etc/php-fpm.d/

RUN docker-php-ext-install mysqli hash

WORKDIR /app

COPY . /app

FROM php:7.2-fpm-alpine

RUN apk add --no-cache php7-pdo_mysql php7-mysqlnd autoconf make gcc g++ nodejs npm &&\
    pecl install xdebug
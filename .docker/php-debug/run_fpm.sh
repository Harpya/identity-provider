#!/usr/bin/env bash
export DOLLAR='$'
envsubst '\$WEBSERVER_PORT' < /root/www.conf > /usr/local/etc/php-fpm.d/www.conf

docker-php-entrypoint php-fpm

FROM harpya/php72-fpm-xdebug:latest

WORKDIR /var/www/html

ARG CACHEBUST=1

COPY  php-debug/xdebug.ini /usr/local/etc/php/conf.d
COPY  php-debug/run_fpm.sh /root

RUN chmod 755 /root/run_fpm.sh

RUN rm -rf /usr/local/etc/php/conf.d/zz-docker.conf

ENV XDEBUG_CONFIG "remote_connect_back=0"
#ENV PHP_IDE_CONFIG="serverName="

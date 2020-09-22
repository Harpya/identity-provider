FROM d9magai/http2apache:latest

# EXPOSE 443
EXPOSE 80

COPY apache.conf /usr/local/apache2/conf/extra/httpd-ssl.conf

RUN adduser www-data

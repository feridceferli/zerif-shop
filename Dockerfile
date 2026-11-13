FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends libsqlite3-dev libpq-dev \
    && docker-php-ext-install pdo_sqlite pdo_pgsql \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite headers \
    && mkdir -p /var/www/html/data /var/www/html/uploads /var/lib/zerif-data/uploads

COPY zerif-shop/ /var/www/html/
RUN cp -a /var/www/html/uploads/. /var/lib/zerif-data/uploads/

RUN chown -R www-data:www-data /var/www/html/data /var/www/html/uploads \
    && chmod 775 /var/www/html/data /var/www/html/uploads \
    && { echo 'ServerTokens Prod'; echo 'ServerSignature Off'; } > /etc/apache2/conf-available/security-custom.conf \
    && a2enconf security-custom

COPY docker/apache-security.conf /etc/apache2/conf-available/zerif-security.conf
RUN a2enconf zerif-security

COPY docker/start.sh /usr/local/bin/zerif-start
RUN chmod +x /usr/local/bin/zerif-start

ENV DB_PATH=/var/lib/zerif-data/shop.sqlite \
    UPLOAD_DIR=/var/lib/zerif-data/uploads \
    PORT=10000
EXPOSE 10000
CMD ["/usr/local/bin/zerif-start"]

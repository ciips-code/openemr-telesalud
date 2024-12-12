FROM selva123is/servers:openemr-telesalud-base

RUN rm -rf /var/www/html \
    && git clone https://github.com/ciips-code/openemr-telesalud.git /var/www/html \
    && cd /var/www/html \
    && composer install \
    && npm install \
    && npm run build \
    # Moving sites so it doesn't get overwritten by volume
    && mv sites sites_temp

ARG TELEHEALTH_BASE_URL
ARG TELEHEALTH_PORT
ARG TELEHEALTH_API_TOKEN
ARG MYSQL_DATABASE
ARG MYSQL_USER
ARG MYSQL_PASSWORD

COPY ./root.env /var/www/html/.env
RUN sed -i "s+VC_API_URL_VALUE+${TELEHEALTH_BASE_URL}+g" /var/www/html/.env \
    && sed -i "s+VC_API_PORT_VALUE+${TELEHEALTH_PORT}+g" /var/www/html/.env \
    && sed -i "s+VC_API_TOKEN_VALUE+${TELEHEALTH_API_TOKEN}+g" /var/www/html/.env \
    && sed -i "s+OPS_DB_USER_VALUE+${MYSQL_USER}+g" /var/www/html/.env \
    && sed -i "s+OPS_DB_PASSWORD_VALUE+${MYSQL_PASSWORD}+g" /var/www/html/.env \
    && sed -i "s+OPS_DB_DATABASE_VALUE+${MYSQL_DATABASE}+g" /var/www/html/.env

COPY ./sqlconf.php /var/www/html/sites_temp/default/sqlconf.php
RUN sed -i "s+MYSQL_USER+${MYSQL_USER}+g" /var/www/html/sites_temp/default/sqlconf.php \
    && sed -i "s+MYSQL_PASSWORD+${MYSQL_PASSWORD}+g" /var/www/html/sites_temp/default/sqlconf.php \
    && sed -i "s+MYSQL_DATABASE+${MYSQL_DATABASE}+g" /var/www/html/sites_temp/default/sqlconf.php

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord"]

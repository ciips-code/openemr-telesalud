FROM ubuntu:23.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt update \
    && apt install -y git-core lsb-release ca-certificates \
    apt-transport-https software-properties-common nginx supervisor

RUN apt install -y php8.1-fpm php8.1-cli  php8.1-common  php8.1-curl \
    php8.1-ldap  php8.1-xml  php8.1-xsl  php8.1-gd  php8.1-zip  php8.1-soap \
    php8.1-mbstring  php8.1-mysql  php8.1-xml  php8.1-redis  php8.1-intl \
    php8.1-bcmath  php8.1-dev  php-pear



COPY php.ini /etc/php/8.1/fpm/php.ini
RUN rm -rf /etc/nginx/sites-enabled/default
COPY openemr.conf /etc/nginx/sites-enabled/openemr.local
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY firstBoot.sh /firstBoot.sh

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

RUN rm -rf /var/www/html \
    && git clone --branch develop https://github.com/selva987/openemr-telesalud.git /var/www/html \
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

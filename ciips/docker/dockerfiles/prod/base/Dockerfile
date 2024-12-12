FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install --fix-missing -y software-properties-common

RUN LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php

RUN apt-get update \
    && apt-get install -y git-core lsb-release ca-certificates \
    apt-transport-https software-properties-common nginx supervisor

RUN apt-get install -y php8.1-fpm php8.1-cli  php8.1-common  php8.1-curl \
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

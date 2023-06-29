FROM ubuntu:23.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt update \
    && apt install -y git-core lsb-release ca-certificates \
    apt-transport-https software-properties-common nginx supervisor

RUN apt install -y php8.1-fpm php8.1-cli  php8.1-common  php8.1-curl \
    php8.1-ldap  php8.1-xml  php8.1-xsl  php8.1-gd  php8.1-zip  php8.1-soap \
    php8.1-mbstring  php8.1-mysql  php8.1-xml  php8.1-redis  php8.1-intl \
    php8.1-bcmath  php8.1-dev  php-pear \
    && pecl channel-update pecl.php.net \
    && pecl install xdebug


#COPY php.ini /etc/php/8.1/fpm/php.ini
RUN rm -rf /etc/nginx/sites-enabled/default
#COPY openemr.conf /etc/nginx/sites-available/openemr.local
#COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

ARG NGINX_UID=1000
RUN usermod -u 10001 ubuntu && groupmod -g 10001 ubuntu
RUN usermod -u ${NGINX_UID} www-data && groupmod -g ${NGINX_UID} www-data

WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord"]

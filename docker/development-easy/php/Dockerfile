FROM php:8.1.9-fpm
# -alpine
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
# aditional
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli pdo pdo_mysql
# apache2 apache2-ssl apache2-utils git php8 php8-tokenizer php8-ctype php8-session php8-apache2 \
# php8-json php8-pdo php8-pdo_mysql php8-curl php8-ldap php8-openssl php8-iconv \
# php8-xml php8-xsl php8-gd php8-zip php8-soap php8-mbstring php8-zlib \
# php8-mysqli php8-sockets php8-xmlreader php8-redis php8-simplexml php8-xmlwriter php8-phar php8-fileinfo \
# php8-sodium php8-calendar php8-intl \

FROM php:8.2-apache

ARG USER="regmon"
ARG UID="1000"

USER root

ENV PATH="/root/.composer/vendor/bin:${PATH}"

RUN useradd -r --no-log-init --create-home --shell /bin/bash --uid "$UID" --user-group "$USER"

RUN apt-get update \
 && apt-get install -y git zlib1g-dev mariadb-client libzip-dev \
 nodejs \
 npm

RUN docker-php-ext-install zip mysqli pdo_mysql

RUN pecl config-set php_ini "${PHP_INI_DIR}/php.ini"
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer



WORKDIR /var/www/html


COPY composer.json composer.json
COPY tests tests
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

ENV PATH="${PATH}:/root/.composer/vendor/bin"

# PHPUnit installieren
RUN composer global require phpunit/phpunit:"^11.5.3"
RUN composer require php-webdriver/webdriver
#RUN ln -s /root/.composer/vendor/phpunit/phpunit/phpunit /usr/local/bin/phpunit
RUN chown -R ${USER}:${USER} /root/.config/composer
RUN chown -R ${USER}:${USER} /var/www/html/vendor





#RUN chmod +x /root/.composer/vendor/bin/phpunit

COPY package.json package.json
RUN npm install

USER ${USER}

FROM ubuntu:latest

RUN apt-get update && apt-get upgrade -y

RUN apt-get update -y && apt-get upgrade -y && \
DEBIAN_FRONTEND="noninteractive" apt-get -y install tzdata \
                                                    curl \
                                                    php7.4 \
                                                    php7.4-curl \
                                                    php7.4-intl \
                                                    php7.4-xml \
                                                    php7.4-mbstring \
                                                    php7.4-ctype \
                                                    php-pdo-mysql \
                                                    apache2 \
                                                    mysql-client \
                                                    wkhtmltopdf \
                                                    git

# Enable PDO
RUN phpenmod pdo_mysql

# Enable URL rewriting
RUN a2enmod rewrite

# Update php configuration
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/7.?/apache2/php.ini
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php/7.?/apache2/php.ini

# Update apache2 configuration
COPY ./docker/apache/sites/000-default.conf /etc/apache2/sites-available/000-default.conf

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Copy this repo into place
WORKDIR /var/www/html
COPY ./app/ /var/www/html
RUN rm /var/www/html/index.html

# To run some additional command during build time (db migrations, loading fixtures, etc)
COPY ./docker/symfony/prepare.sh .
RUN chmod a+x ./prepare.sh

# Install composer
RUN curl --show-error --silent https://getcomposer.org/installer | php \
    && php composer.phar install -v \
    && cp composer.phar /usr/bin/composer

# restart apache2
RUN service apache2 restart
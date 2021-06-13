FROM ubuntu:latest

# Prerequisites install
RUN apt-get update -y && apt-get upgrade -y && \
DEBIAN_FRONTEND="noninteractive" apt-get -y install tzdata && \
apt-get install apache2 php php-mysql php-xml php-mbstring git zip unzip --fix-missing -y

# Enable PDO, URL rewriting and htaccess
RUN phpenmod pdo_mysql
RUN a2enmod rewrite
RUN sed -i '172s/None/All/' /etc/apache2/apache2.conf

# Lab initialization
RUN rm /var/www/html/index.html
COPY ./app /var/www/html
COPY ./bin/apache2/000-default.conf /etc/apache2/sites-enabled/000-default.conf
WORKDIR /var/www/html
COPY --chown=1000:1000 ./app /var/www/html/
RUN PATH=$PATH:/var/www/html/vendor/bin:bin

# Install composer and vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-scripts --prefer-dist --no-interaction

# Restart apache2
RUN service apache2 restart

# Make migrations
RUN php bin/console doctrine:migrations:migrate
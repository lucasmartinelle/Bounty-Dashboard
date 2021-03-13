FROM ubuntu:latest

# Install dependencies
RUN apt-get update && apt-get -y upgrade && DEBIAN_FRONTEND=noninteractive \
    apt-get -y install apache2 php curl php-pdo-mysql mysql-client

# Enable PDO
RUN phpenmod pdo_mysql

# Enable URL rewriting
RUN a2enmod rewrite

# Update php configuration
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/7.?/apache2/php.ini
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php/7.?/apache2/php.ini

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Copy this repo into place
RUN mkdir /var/www/html/Bounty-Dashboard
COPY . /var/www/html/Bounty-Dashboard
WORKDIR /var/www/html/Bounty-Dashboard

#RUN mysql -u root -h dbbountydash -ppassword bugbounty < base.sql

# Update the default apache site with the config we created.
COPY apache-config.conf /etc/apache2/sites-enabled/000-default.conf
RUN service apache2 restart
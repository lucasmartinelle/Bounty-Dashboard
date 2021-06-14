# RUN MIGRATIONS
cd /var/www/html
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# GIVE PERMISSIONS
chown www-data:www-data /var/www/html -R && chmod 775 /var/www/html -R

# UPDATE ENV
sed -i -e "s/APP_ENV=dev/APP_ENV=prod/g" .env
sed -i -e "s/APP_DEBUG=1/APP_DEBUG=0/g" .env
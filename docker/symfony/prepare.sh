# RUN MIGRATIONS
echo -e "\e[36mRunning \e[33mMigrations and Fixtures"
cd /var/www/html
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# RUN FIXTURES
php bin/console doctrine:fixtures:load

echo -e "\e[33mMigrations and Fixtures \e[36mDone"
echo -e "Updating \e[33menvironment"

# UPDATE ENV
# sed -i -e "s/APP_ENV=dev/APP_ENV=prod/g" .env
# sed -i -e "s/APP_DEBUG=1/APP_DEBUG=0/g" .env

echo -e "Env \e[36mUpdated"
echo -e "Giving \e[33mpermissions"

# GIVE PERMISSIONS
chown www-data:www-data /var/www/html -R && chmod 775 /var/www/html -R

echo -e "Permissions \e[36mGived"

echo -e "\e[31mPrepare script Done.\e[39m"
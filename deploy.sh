#!/bin/bash

echo "Fetching file from $1\n"
wget https://github.com/ChrisBooker/office-league/archive/$1.tar.gz --directory-prefix=source --no-check-certificate

echo "Unpacking....."
tar -zxvf source/$1 --strip 1 -C source

echo "Install Frontend..."
rsync -r source/httpdocs-front/ [YOUR_FRONTEND_DIRECTORY]

echo "Install API..."
rsync -r source/httpdocs-api/ [YOUR_API_DIRECTORY]

echo "Updating Configs..."
find [YOUR_API_DIRECTORY]/classes/Config.php -type f -exec sed -i 's/@API_HOST/[YOUR_API_HOST]/g' {} \;
find [YOUR_API_DIRECTORY]/classes/AbstractData.php -type f -exec sed -i 's/@PDO_DB/[YOUR_DB_NAME]/g' {} \;
find [YOUR_API_DIRECTORY]/classes/AbstractData.php -type f -exec sed -i 's/@PDO_USER/[YOUR_DB_USER]/g' {} \;
find [YOUR_API_DIRECTORY]/classes/AbstractData.php -type f -exec sed -i 's/@PDO_PASS/[YOUR_DB_PASS]/g' {} \;

find [YOUR_FRONTEND_DIRECTORY]/classes/Config.php -type f -exec sed -i 's/@API_HOST/[YOUR_API_HOST]/g' {} \;
find [YOUR_FRONTEND_DIRECTORY]/classes/Config.php -type f -exec sed -i 's/@FRONTEND_HOST/[YOUR_FRONTEND_HOST]/g' {} \;

echo "Housekeeping..."
rm -rf source


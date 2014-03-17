#!/bin/bash

###
# Deployment directly from a github release
#
# Usage: deploy.sh [tag]
# 
# Note: Doesn't take into account any schema changes
###

echo "Fetching file from $1\n"
wget https://github.com/ChrisBooker/office-league/archive/$1.tar.gz --directory-prefix=source --no-check-certificate

echo "Unpacking....."
tar -zxf source/$1 --strip 1 -C source

echo "Install Frontend..."
rsync -r source/httpdocs-front/ [YOUR_FRONTEND_DIR]

echo "Install API..."
rsync -r source/httpdocs-api/ [YOUR_API_DIR]

echo "Updating Configs..."
find [YOUR_API_DIR]/classes/Config.php -type f -exec sed -i 's/@API_HOST/fifa-api.kipspace.co.uk/g' {} \;
find [YOUR_API_DIR]/classes/AbstractData.php -type f -exec sed -i 's/@PDO_DB/[YOUR_DB_NAME]/g' {} \;
find [YOUR_API_DIR]/classes/AbstractData.php -type f -exec sed -i 's/@PDO_USER/[YOUR_DB_USER]/g' {} \;
find [YOUR_API_DIR]/classes/AbstractData.php -type f -exec sed -i 's/@PDO_PASS/[YOUR_DB_PASS]/g' {} \;

find [YOUR_FRONTEND_DIR]/classes/Config.php -type f -exec sed -i 's/@API_HOST/[YOUR_API_DIR]/g' {} \;
find [YOUR_FRONTEND_DIR]/classes/Config.php -type f -exec sed -i 's/@FRONTEND_HOST/[YOUR_FRONTEND_DIR]/g' {} \;

echo "Housekeeping..."
rm -rf source

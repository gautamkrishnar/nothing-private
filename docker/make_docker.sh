#!/usr/bin/env bash
sed -i  's/\"https:\/\/nothing-private-api.gautamkrishnar.com\"/document.location.href + \"\db_server\"/g' /var/www/html/main.js
cp -fv /var/www/html/docker/docker-connection.php /var/www/html/db_server/connection.php

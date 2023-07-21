#!/usr/bin/env bash
sed -i  's/\"https:\/\/npvt-api.gkr.pw\"/document.location.href + \"\db_server\"/g' /var/www/html/main.js
cd /var/www/html/db_server
php composer.phar install

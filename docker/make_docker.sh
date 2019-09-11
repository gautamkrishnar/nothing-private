sed -i  's/window.location.protocol + \"\/\/nothingprivate.000webhostapp.com\"/document.location.href + \"\db_server\"/g' /var/www/html/main.js
cp -fv /var/www/html/docker/docker-connection.php /var/www/html/db_server/connection.php

FROM php:7.4-apache
COPY . /var/www/html/
RUN  apt-get update && \
         apt-get install -y git zlib1g-dev libzip-dev && \
         docker-php-ext-install mysqli && \
         docker-php-ext-install zip
RUN cd /var/www/html/db_server/ && \
         bash /var/www/html/docker/install_composer.sh && \
         bash /var/www/html/docker/make_docker.sh

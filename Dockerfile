FROM php:7.3-apache
COPY . /var/www/html/
RUN docker-php-ext-install mysqli
RUN bash /var/www/html/docker/make_docker.sh

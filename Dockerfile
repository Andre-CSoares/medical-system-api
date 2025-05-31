FROM php:8.2

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY . .

# Instalar wait-for-it para aguardar o MySQL
ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Script modificado para aguardar o banco
CMD wait-for-it db:3306 --timeout=60 --strict -- php artisan serve --host=0.0.0.0 --port=8000
CREATE USER IF NOT EXISTS 'laravel_user'@'%' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON laravel_api.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;
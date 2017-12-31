FROM richard87/php-base

COPY composer.json composer.lock symfony.lock ./
RUN touch .env
RUN composer install --no-scripts --no-autoloader

COPY ./ ./
RUN composer dump-autoload -o
RUN php bin/console cache:warmup
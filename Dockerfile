FROM richard87/php-base

RUN pecl install protobuf grpc && docker-php-ext-enable protobuf grpc

COPY composer.json composer.lock symfony.lock ./
RUN touch .env
RUN composer install --no-scripts --no-autoloader

COPY ./ ./
RUN composer dump-autoload -o
RUN php bin/console cache:warmup
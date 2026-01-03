FROM php:8.2-fpm
ENV TZ=Asia/Tehran
ENV ACCEPT_EULA=Y
WORKDIR /var/www/talentyar

RUN apt-get -y update && apt-get -y install libssl-dev libcurl4-openssl-dev wget zip apt-utils libzip-dev libxml2-dev \
                                            libssh-dev git supervisor cron zlib1g-dev libjpeg62-turbo-dev libpng-dev \
                                            libxpm-dev libfreetype6-dev iputils-ping redis-server

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN apt-get update -yqq \
    && apt-get install -y --no-install-recommends openssl \
    && sed -i 's,^\(MinProtocol[ ]*=\).*,\1TLSv1.0,' /etc/ssl/openssl.cnf \
    && sed -i 's,^\(CipherString[ ]*=\).*,\1DEFAULT@SECLEVEL=1,' /etc/ssl/openssl.cnf \
    && rm -rf /var/lib/apt/lists/*

RUN pecl config-set php_ini $PHP_INI_DIR/php.ini

RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    && docker-php-ext-install \
        curl bcmath zip iconv xml soap pcntl pdo pdo_mysql simplexml gd opcache

COPY Supervisor/talentyar.conf /etc/supervisor/conf.d

COPY www-custom.conf /usr/local/etc/php-fpm.d/www.conf

RUN sed -i 's/listen = 9000/listen = 0.0.0.0:9001/' /usr/local/etc/php-fpm.d/zz-docker.conf

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN printf '[PHP]\ndate.timezone="Asia/Tehran"\n' > /usr/local/etc/php/conf.d/tzone.ini

RUN { \
  echo 'memory_limit=4096M'; \
  echo 'max_execution_time=60'; \
  echo 'max_input_time=60'; \
  echo 'post_max_size=256M'; \
  echo 'upload_max_filesize=256M'; \
  echo 'realpath_cache_size=4096K'; \
  echo 'realpath_cache_ttl=600'; \
  echo 'display_errors=0'; \
  echo 'log_errors=1'; \
} > /usr/local/etc/php/conf.d/99-performance.ini


RUN { \
  echo 'opcache.enable=1'; \
  echo 'opcache.enable_cli=0'; \
  echo 'opcache.memory_consumption=1024'; \
  echo 'opcache.interned_strings_buffer=16'; \
  echo 'opcache.max_accelerated_files=40000'; \
  echo 'opcache.validate_timestamps=0'; \
  echo 'opcache.save_comments=1'; \
  echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/99-opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer


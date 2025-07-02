FROM php:8.3.7-apache-bookworm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libz-dev libssl-dev libprotobuf-dev \
    pkg-config zlib1g-dev libcurl4-openssl-dev \
    autoconf build-essential libtool cmake protobuf-compiler gdb

# Create log directory
RUN mkdir -p /var/log/mylogs

# Composer (optional)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV GRPC_TRACE=api,channel,http,connectivity_state
ENV GRPC_TRACE=all
ENV GRPC_VERBOSITY=debug

WORKDIR /var/www/html

RUN git clone -b v1.69.0 https://github.com/grpc/grpc && \
    cd grpc && \
    git submodule update --init && \
    EXTRA_DEFINES=GRPC_POSIX_FORK_ALLOW_PTHREAD_ATFORK make

RUN cd /var/www/html/grpc/src/php/ext/grpc && \
    phpize && \
    CFLAGS="-g -O0" GRPC_LIB_SUBDIR=libs/opt ./configure --enable-grpc="/var/www/html/grpc" && \
    make && \
    make install && \
    ldconfig

RUN echo "extension=grpc.so" > /usr/local/etc/php/conf.d/docker-php-ext-grpc.ini

# Copy app
COPY ./app /var/www/html

RUN composer install

# Default command
CMD php /var/www/html/index.php 2> /var/log/mylogs/grpc.log

FROM php:5.6.31-apache

ENV PHP_CPPFLAGS="$PHP_CFLAGS -std=c++11"
RUN export CPPFLAGS="$PHP_CPPFLAGS"

ENV IMG_TEMP_SOFT \
    libicu-dev \
    libxml2-dev \
    libssl-dev \
    libmagickwand-dev \
    libmagickcore-dev \
    libmcrypt-dev

RUN apt-get update && \
    apt-get install \
        --no-install-recommends \
        --yes \
            zip \
            unzip \
            curl \
            git \
            ssmtp \
            $IMG_TEMP_SOFT

RUN \
    curl -SL https://sourceforge.net/projects/icu/files/ICU4C/59.1/icu4c-59_1-src.tgz | tar xz -C /usr/src/ && \
    cd /usr/src/icu/source && \
    chmod u+x \
        runConfigureICU \
        configure \
        install-sh && \
    ./runConfigureICU Linux && \
    ./configure --prefix=/usr/local && \
    make && \
    make check && \
    make install && \
    rm -r /usr/src/icu

RUN \
    a2enmod rewrite && \
    a2enmod headers && \
    a2enmod ssl

RUN \
    docker-php-ext-install \
        opcache \
        intl \
        soap \
        exif \
        mysqli \ 
        pdo \
        pdo_mysql \
        mcrypt && \ 
    docker-php-ext-configure intl --with-icu-dir=/usr/local && \
    pecl install \
        mongo-1.2.12 \
        imagick && \
    docker-php-ext-enable \
        mongo \
        imagick

RUN \
    curl -SL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./include/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./include/php.ini /usr/local/etc/php/php.ini
COPY ./include/ssmtp.conf /etc/ssmtp/ssmtp.conf
COPY ./include/post-install.sh /usr/bin/post-install.sh

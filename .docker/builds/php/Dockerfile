FROM php:8.1.21-fpm-alpine

ENV user app
ARG PUID=1000
ARG PGID=1000
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
# Install composer
COPY --from=docker.io/library/composer:2.4.4 /usr/bin/composer /usr/bin/
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="./vendor/bin:$PATH"
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

RUN addgroup -g $PGID $user \
  && adduser -u $PUID -G $user -S $user \
  && apk add --update-cache --no-cache $PHPIZE_DEPS \
  linux-headers \
  nodejs \
  npm \
  yarn \
  mysql-client \
  libbz2 \
  zip \
  git \
  jpegoptim \
  optipng \
  pngquant \
  ffmpeg \
  ghostscript \
  fcgi \
  ### Required for Chrome in vue part
  nss \
  cups-libs \
  libxcomposite \
  libc6-compat \
  at-spi2-core \
  libxkbcommon \
  udev \
  ttf-freefont \
  chromium \
  ###
  icu-dev \
  zlib-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  libzip-dev \
  libxml2-dev \
  libmcrypt-dev \
  # # Setup locale
  # && echo en_US.UTF-8 UTF-8 > /etc/locale.gen && locale-gen \
  # Install and enable PECL extensions
  && npm install puppeteer@19.7.2 \
  && pecl install -o -f redis \
  xdebug \
  mailparse \
  mcrypt \
  && docker-php-ext-enable redis \
  xdebug \
  mailparse \
  mcrypt \
  && install-php-extensions imagick \
  && docker-php-ext-configure intl \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  intl \
  mysqli \
  pdo_mysql \
  pcntl \
  gd \
  zip \
  soap \
  bz2 \
  exif \
  bcmath \
  opcache \
  && curl -o /usr/local/bin/php-fpm-healthcheck \
  https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
  && chmod +x /usr/local/bin/php-fpm-healthcheck

# Update PHP config
RUN echo "max_execution_time=1000" > $PHP_INI_DIR/conf.d/max-execution-time.ini && \
  echo "request_terminate_timeout=300" > $PHP_INI_DIR/conf.d/max-execution-time.ini && \
  echo "display_errors=stderr" > $PHP_INI_DIR/conf.d/display-errors.ini && \
  echo "expose_php=0" > $PHP_INI_DIR/conf.d/path-info.ini

ADD policy.xml /etc/ImageMagick-6/policy.xml

# memory-limit
ADD memory-limit.ini /usr/local/etc/php/conf.d/

USER $user
EXPOSE 9000
CMD ["php-fpm"]

FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl netcat-openbsd sudo \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Xserver 環境模倣
# - サーバーデプロイ時はホームディレクトリ内のファイルおよびPHPのパスは実際の環境に合わせて変更すること
RUN sed -i 's#/var/www/html#/home/app_user/domain_name/public_html#' /etc/apache2/sites-enabled/000-default.conf \
  && sed -i 's#/var/www/html#/home/app_user/domain_name/public_html#' /etc/apache2/sites-available/default-ssl.conf \
  && echo '<Directory /home/app_user/domain_name/public_html/>' >> /etc/apache2/apache2.conf \
  && echo '        Options Indexes FollowSymLinks' >> /etc/apache2/apache2.conf \
  && echo '        AllowOverride All' >> /etc/apache2/apache2.conf \
  && echo '        Require all granted' >> /etc/apache2/apache2.conf \
  && echo '</Directory>' >> /etc/apache2/apache2.conf \
  && echo '' \
  && echo 'DirectoryIndex index.php index.html' >> /etc/apache2/apache2.conf \
  && a2enmod rewrite \
  && a2ensite default-ssl \
  && a2enmod ssl \
  && a2enmod proxy proxy_http
RUN groupadd -g 1000 app_user \
  && useradd -u 1000 -g app_user -m app_user \
  && echo "app_user:password" | chpasswd \
  && chmod +x /home/app_user \
  && mkdir -p /home/app_user/domain_name/public_html \
  && mkdir -p /home/app_user/domain_name/api
RUN chown -R app_user:app_user /home/app_user

# Xserver セットアップ リハーサル
USER app_user

## Settings
RUN mkdir -p /home/app_user/domain_name/api/public \
  && chmod 777 /home/app_user/domain_name/api \
  && ln -s /home/app_user/domain_name/api/public /home/app_user/domain_name/public_html/backend
RUN echo 'Options -Indexes' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteEngine On' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteCond %{HTTPS} off' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteCond %{REQUEST_URI} ^/api' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteRule ^api/(.*)$ backend/api/$1 [QSA,L]' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteCond %{REQUEST_URI} ^/assets/.*$ [NC]' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteRule ^ - [L]' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteCond %{REQUEST_FILENAME} !-f' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteCond %{REQUEST_FILENAME} !-d' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo 'RewriteRule ^(.*)$ index.html [L]' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '' >> /home/app_user/domain_name/public_html/.htaccess
RUN mkdir /home/app_user/bin \
  && ln -s /usr/local/bin/php /home/app_user/bin/php \
  && echo 'PATH=$HOME/bin:$HOME/.config/composer/vendor/bin:$PATH' >> /home/app_user/.bash_profile

## Install Composer
WORKDIR /home/app_user
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mkdir -p .config/composer/vendor/bin/ \
  && mv composer.phar .config/composer/vendor/bin/composer

# ローカル開発用
USER root

WORKDIR /home/app_user/domain_name/api
EXPOSE 443

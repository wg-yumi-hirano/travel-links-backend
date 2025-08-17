FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl netcat-openbsd \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Xserver
# - ローカル環境が Windows の場合に権限の模倣が困難なため、ローカル環境ではすべて root で操作するものとする
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
  && groupadd -g 1000 app_user \
  && useradd -u 1000 -g app_user -m app_user \
  && chmod +x /home/app_user \
  && mkdir -p /home/app_user/domain_name/public_html \
  && a2ensite default-ssl \
  && a2enmod ssl

# Settings
RUN mkdir -p /home/app_user/domain_name/app/public \
  && ln -s /home/app_user/domain_name/app/public /home/app_user/domain_name/public_html \
  && echo '<IfModule mod_rewrite.c>' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '    RewriteEngine On' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '    RewriteRule ^(.*)$ public/$1 [QSA,L]' >> /home/app_user/domain_name/public_html/.htaccess \
  && echo '</IfModule>' >> /home/app_user/domain_name/public_html/.htaccess
# RUN mkdir /home/app_user/bin \
#   && ln -s /usr/local/bin/php /home/app_user/bin/php \
#   && echo 'PATH=$HOME/bin:$HOME/.config/composer/vendor/bin:$PATH' >> /home/app_user/.bash_profile
# WORKDIR /home/app_user

# # Install Composer
# RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
#   && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" \
#   && php composer-setup.php \
#   && php -r "unlink('composer-setup.php');" \
#   && mkdir -p .config/composer/vendor/bin/ \
#   && mv composer.phar .config/composer/vendor/bin/composer

COPY . /home/app_user/app

EXPOSE 443

WORKDIR /home/app_user/app

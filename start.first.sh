#!/usr/bin/env bash

MY_IP=`curl inet-ip.info`

if [ "${MY_IP}" = "106.161.242.135" ]; then
    # Local Environment
    ENV_NAME='local'
else
    # PROD Environment
    ENV_NAME='prod'
fi

cp docker-compose.${ENV_NAME}.yml docker-compose.yml

# Install PHP
if which php > /dev/null 2>&1; then
    echo 'PHP has already installed'
else
    # on EC2
    echo 'Install PHP'
    sudo amazon-linux-extras install php7.2
    sudo yum install -y php-mbstring php-xml
    echo 'PHP installed'
fi

# Install Composer
if which composer > /dev/null 2>&1; then
    echo 'Composer has already installed'
else
    echo 'Install Composer'
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
    echo 'Composer installed'
fi

cd laravel && composer install
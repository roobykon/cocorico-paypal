# Cocorico PayPal Edition

Cocorico together with the installation instructions at [https://github.com/Cocolabs-SAS/cocorico](https://github.com/Cocolabs-SAS/cocorico).
Cocorico General Functional Specifications at : [http://wiki.roobykon.com/index.php/Cocorico_General_Functional_Specifications](http://wiki.roobykon.com/index.php/Cocorico_General_Functional_Specifications)

## Requirements

- [ Ubuntu ]( doc/OS.md ) **17.04**
- [ Zip ]( doc/ZIP.md ) **3.0**
- [ Git ]( doc/GIT.md ) **2.7.4**
- [ Composer ]( doc/COMPOSER.md ) **1.4.2**
- [ Php ]( doc/PHP.md ) **5.6.31**
- [ Apache ]( doc/APACHE.md ) **2.4.18**
- [ MySQL ]( doc/MYSQL.md ) **5.7.19**
- [ MongoDB ]( doc/MONGODB.md ) **3.4.7**
- [ Postfix ]( doc/POSTFIX.md ) **3.1.0**

## Deploy

> download vendors

    composer install

> set directory permissions

    sudo chmod 0755 /app/cache -R
    sudo chmod 0755 /app/logs -R
    sudo chmod 0755 /web/uploads -R

> rename .htaccess

    cp web/.htaccess.staging.dist > web/.htaccess

> up virtual host

    sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/auto-guide.roobykon.com.conf
    sudo vim /etc/apache2/sites-available/auto-guide.roobykon.com.conf
    sudo a2ensite auto-guide.roobykon.com

> disable default virtual host

    sudo a2dissite 000-default.conf

> enable mod rewrite

    sudo a2enmod rewrite

> dumping assets

    php app/console assetic:dump --env=staging

> create db structure

    php app/console doctrine:schema:create

> load fixtures

    php app/console doctrine:fixtures:load

> update currencies

    php app/console cocorico:currency:update

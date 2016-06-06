TindaJaroPH
===========

To install the application, please do:

    php composer.phar install

Or, if you have global installation of composer

    composer install

This will download and locally install the framework and libraries used by the application.

Generate database:

    php bin/console doctrine:database:create
    
And generate the schema:

    php bin/console doctrine:schema:update --force

To run the application, please do:

    php bin/console server:run

> Thank you. :)

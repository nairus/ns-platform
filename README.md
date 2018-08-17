# NS Platform project

A Symfony project created on July 17, 2017, 7:37 pm.
This is a web platform internationalized for personal website, resumes, blog posts and other stuff.

## Install

1.  Download the source code or clone repository from master branch like this:

    ```bash
    git clone https://github.com/nairus/ns-platform.git ns-platform
    ```

1.  Install dependencies via [composer](https://getcomposer.org/):

    ```bash
    composer install
    ```

1.  Install database:

    1.  Configure database

        Copy the file "parameters.yml.dist" without ".dist" extension in "app/config.  
        Change the parameters for dev or production use.

        ```yml
        parameters:
          database_host: localhost
          database_port: ~
          database_name: mydatabase
          database_user: mydbuser
          database_password: myawesomepassword
        ```

    1.  Create the database

        - With doctrine command if the dbuser is allowed:

        ```bash
        php bin/console doctrine:database:create
        ```

        Otherwise with sql command

        - Create the schema via doctrine:

        ```bash
        php bin/console doctrine:schema:create
        ```

1.  Install assets

    ```bash
    php bin/console assets:install
    ```

1. Configure Web server
    
    *Note: I presume you have installed the web server wanted first.  
    Or see the [apache 2.4 installation](http://httpd.apache.org/docs/2.4/install.html) for example.*

    1. Apache 2.4  
        1. Enable rewrite module
            ```shell
            cd /etc/apache2/mods-enabled
            ln -s ../mods-available/rewrite.load 
            ```
        1. Enable vhost module
            ```shell
            ln -s ../mods-available/vhost_alias.load
            ```
        1. Add virtual host config
            * Create the file:
            ```bash
            touch /etc/apache2/sites-available/001-ns-platform.conf 
            ```
            * Example of virtual host:
            ```apache
            <VirtualHost *:80>
                ServerName ns-platform.localhost
                ServerAlias ns-platform.localhost

                ServerAdmin webmaster@localhost
                DocumentRoot /var/www/ns-platform/web

                DirectoryIndex app.php
                <Directory /var/www/ns-platform/web>
                    Options Indexes FollowSymlinks
                    AllowOverride All
                </Directory>

                # optionally disable the fallback resource for the asset directories
                # which will allow Apache to return a 404 error when files are
                # not found instead of passing the request to Symfony
                <Directory /var/www/ns-platform/web/bundles>
                        FallbackResource disabled
                </Directory>

                ErrorLog ${APACHE_LOG_DIR}/error.log
                CustomLog ${APACHE_LOG_DIR}/access.log combined
            </VirtualHost>
            ```
        1. Enable virtual host and restart apache
            ```shell
            cd /etc/apache2/sites-enabled
            ln -s ../sites-available/001-ns-platform.conf
            /etc/init.d/apache2 restart
            ```
    1. Other configurations  
        You can go futher using `Nginx` server for example.  
        For more details see this [online doc](https://symfony.com/doc/3.4/setup/web_server_configuration.html).

## Development tips

Since PHP 5.4, php binary provide a [dev web server](https://secure.php.net/manual/en/features.commandline.webserver.php).  
To use this dev webserver with symfony, we can install it as a dev bundle [`web-server-bundle`](https://github.com/symfony/web-server-bundle) with this command:

```bash
composer require symfony/web-server-bundle --dev
```

**Note: This dependency is already in the repository.**

Then run the dev server like this:

```bash
php bin/console server:run --docroot=web
```

**/!\ If you use an apache server with vhost module, don't forget to add your local ip in the `/etc/hosts` file.**

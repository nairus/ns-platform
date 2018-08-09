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

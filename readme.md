# CRM Api v1.0
Api Collection for segmentation and data management

Developed using Laravel Framework 5.5
if you need to check exact framework version run `php artisan -V`

## Requisites
* PHP >= 7.0.0
* Mysql
* Nginx
* Redis
* Node
* npm

Php Modules
* Curl
* Fileinfo
* Imap
* Json
* OpenSSL
* PDO
* Mbstring
* Tokenizer
* XML
* bcmath

Node dependencies
* libtool
* automake
* autoconfnasm
* libpng-dev

[Image optimizer dependecies](https://github.com/spatie/image-optimizer#optimization-tools)
* JpegOptim
* Optipng
* Pngquant 2
* SVGO
* Gifsicle

## First Deploy
1. Setup deploy ssh keys at gitlab project configuration.
2. [Install composer](https://getcomposer.org/download/) and [Setup globally](https://getcomposer.org/doc/00-intro.md#globally)
3. Clone the project into your local drive: `git clone git@github.com:luisferparra/lrv-segmentation-api.git lrv-crm-api`
4. `cd lrv-crm-api`
5. `cp .env.example .env` and setup .env with correct values.
6. `composer install --no-dev`
7. `php artisan migrate` will populate database
8. `npm install` will install front dependencies
9. `npm run prod` will generate front assets

## MySql considerations
This api uses 4 DB Schemas. All schemas must be created.
You must create database  to configure .env
All charset are `utf8` and collations `utf8_unicode_ci` so use default charset according to this.
```sql
CREATE SCHEMA `crm-api` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
CREATE SCHEMA `crm-api-segmentation` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
CREATE SCHEMA `crm-api-temp` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
CREATE SCHEMA `crm-data` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
```

### First time database populate
The application has a few sync processes to copy data from CRM database to Kami database. The first deploy need to run this commands manually to avoid waiting until sync time.
```bash
php artisan crm:sync:bbdd
php artisan crm:syncInstall:users
php artisan statistics:home:create
php artisan crm:sync:columns
```




## Grant Api Access
For accesing the Apis, the library Passport must be installed (not only with composer). So follow the next step:

```bash
php artisan passport:install
```

## Update & deploy
```bash
cd /path-to-your-project/
```
As we have the project deployed as nginx we need to switch to nginx user (this user also has the deploy key configured at git server)

```bash
sudo -Hu nginx bash
```
Now we are at proyect root path and we are the right user

```bash
git pull origin master && composer install --no-dev && php artisan migrate && npm install && npm run prod
```

## Supervisor

## Crontab
You must call [laravel task schreduler](https://laravel.com/docs/5.5/scheduling)
```
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

## Cache
It is highly recommended to Cache both Routes and Config. So, run the following artisan commands

```bash
php artisan route:cache
php artisan config:cache
```
Notice that if any config file is modified or is added a new one, the cache creation must be re-executed.
Same policy for Routes.

# Nginx configuration
Initial Setup will be something like this
```nginx
server {
    listen 80;

    root /var/www/myapp/public;

    index index.php;

    server_name kami.netsales.es;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
       include snippets/fastcgi-php.conf;
       fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
    }
}
```

# Run tests
At project folder run `vendor/phpunit/phpunit/phpunit`

### A Netsales Product (c) 2017
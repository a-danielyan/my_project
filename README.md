# Mvix CRM

## Installation/Setup BE project on local development environment

### 1. Install Docker + Docker Compose

Follow official documentation to install docker and docker-compose:

- **Docker**: https://docs.docker.com/install/
- **Docker Compose**: https://docs.docker.com/compose/install/

### 2. Setup local docker-compose enviornment

To configure the `docker-compose` environment, go to the `.docker`  directory and copy file `.env.dist` to `.env`. If you need please apply changes.

```
COMPOSE_PROJECT_NAME=mvix_crm

MYSQL_HOST_PORT=3307 // port of host machine for access to database
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=xhb
MYSQL_USERNAME=root
MYSQL_PASSWORD=root

NGINX_PORT=8000 // port of host machine for access to app
APP_KEY=someKey
JWT_SECRET=A6H4JCR8RXkdw7LjiYy17hodIvxiD6nsqmQ0MLHtq9yeTg7bbyDlP1YXPIaC6lzp
REDIS_HOST_PORT=6379


PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
```

### 3. Create and start containers

Go to the directory `.docker` and run `docker-compose up -d`.

Run `docker-compose ps` command to verify the running containers status, you should see the following result:

```
NAME                    COMMAND                  SERVICE             STATUS              PORTS
mvix-crm-mysql-1        "docker-entrypoint.s…"   mysql               running             33060/tcp, 0.0.0.0:3307->3306/tcp
mvix-crm-mysql_test-1   "docker-entrypoint.s…"   mysql_test          running             3306/tcp, 33060/tcp
mvix-crm-nginx-1        "/docker-entrypoint.…"   nginx               running             0.0.0.0:8000->80/tcp
mvix-crm-php-1          "docker-php-entrypoi…"   php                 running             0.0.0.0:8081->8081/tcp, 9000/tcp
mvix-crm-redis-1        "docker-entrypoint.s…"   redis               running             0.0.0.0:6379->6379/tcp
```

#### Exec into container shell

To work with the app, you can access container bash shell using the following command: `docker-compose exec php sh`

Inside php app container bash shell, you can run all necassary commands:

#### Local development environment:
Before starting your work on the local environment, run following commands:

- `php artisan --env=local config:cache`
- `php artisan --env=local migrate`
- `php artisan --env=local data-inject`


With default ENV configuration, BE API will be accessible from http://localhost:8000/


### 4. Configure and use Xdebug
Xdebug is a PHP extension which provides debugging and profiling capabilities. It uses the DBGp debugging protocol.

Setup and using Xdebug on Docker environment and PhpStorm for the [current project](https://gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend/-/wikis/Setup-and-using-Xdebug-on-Docker-environment-and-PhpStorm)

#### Test environment:
To work with the test environment, run following commands:

1. `php artisan --env=testing config:cache`
2. `php artisan --env=testing migrate:fresh` - run migration
3. `php artisan --env=testing db:seed --class=MinimalDatabaseSeeder` - run database seeder with minimal data required to run tests
4. `php artisan --env=testing db:seed --class=TestDatabaseSeeder` - run database seeder with tests specific  data


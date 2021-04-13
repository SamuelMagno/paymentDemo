# Payment Demo API
 
## SETUP

To run this project you'll need to install on your machine:
- Docker

## RUN SERVER

Inside the root folder of the project, using any terminal, run the command line below to start the docker containers in localhost:

`docker-compose up -d`

## SETUP ENVIRONMENT

Copy the `/app/.env.example` file to a new file named ".env"

In the .env file, change the lines:

	DB_CONNECTION=mysql

	DB_HOST=127.0.0.1

	DB_PORT=3306

	DB_DATABASE=laravel

	DB_USERNAME=root

	DB_PASSWORD=
	
to

	DB_CONNECTION=mysql

	DB_HOST=app-mysql

	DB_PORT=3306

	DB_DATABASE=laravel

	DB_USERNAME=root

	DB_PASSWORD=laravel

Add a new line to configure the database used for tests:

	DB_TEST_DATABASE=laraveltest
    
Run the command to generate artisan key:

`docker exec -it app-php-fpm bash -c "composer update && php artisan key:generate"`

## CREATE DATABASE

Run the command below to create tables, columns and mock users in database:

`docker exec -it app-php-fpm bash -c "php artisan migrate --seed"`

## CREATE TEST DATABASE

Run the commands below to create the test database:

`docker exec -it app-php-fpm bash -c "php artisan db:create laraveltest"`

`docker exec -it app-php-fpm bash -c "php artisan migrate --database=test"`

## RUN TEST

`docker exec -it app-php-fpm bash -c "php artisan test"`

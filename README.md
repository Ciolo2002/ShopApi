#Shop And Offers

## Description
This is a simple web application in PHP 8.2 with Laravel 11 that allows sync shops and offers from APIs. Users get data from the API and can filter the data by APIs.
####This application has no GUI


##Prerequisites
- Docker >= 24.0.2
- Docker Compose >= 1.29.2

## Installation
- Clone the repository
- Move to the root directory of the project (where the docker-compose.yml file is located)
- Create an empty directory called `mysql` in the root directory
- Create a .env file in the root directory `cp .env.example .env`
  - Set the values of the environment variables in the .env file
- Move into code/shop and create a .env file `cp .env.example .env`
  - Set the values of the environment variables in the .env file
- Move to the root directory of the project (where the docker-compose.yml file is located)
- Run `docker-compose up -d --build`
  - Automatically the application will turn on and it will run the migration and will be listening on http://localhost:${APACHE_PORT} where APACHE_PORT is the port set in the .env file


##To sync data
- Move to the root directory of the project (where the docker-compose.yml file is located)
- Run `docker-compose exec -T web php artisan app:sync-all`

###To schedule the sync
- Add a cron job every minute that runs the command `docker-compose exec -T web php artisan schedule:run >> /dev/null 2>&1`


##Exposed Endpoints
- [GET] /api/v1/offers/{shop_id} - Get all offers from a shop sorted by price in descending order
- [GET] /api/v1/offers/{country} - Get all offers from a country
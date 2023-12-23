# Url shortener

Simple url shortener

## PHP and laravel version:
- PHP: 8.1
- Laravel: 10.10


## Features
- Authenticated user can create short url
- All users can convert short url to main url
- Authenticated user can see owned shorted url

## Tests

For every step there is test and you can run tests
```bash
php artisan test
```

## Validations
all validations are in `App\Http\Requests` and there is `BaseRequest` class that all validation extends from it, all validations handled with Laravel form request

## Authentication

all users can convert shorted url to main url but only authenticated users can create short url and see owned shorted urls.


## Models

There is 3 models:
- User
- Url
- Visit

## Models relations
- every user can have multiple urls
- every url can have multiple visits

## High scale solution

i created caching functionality for convert shorted url to main url to scale high rate users.

## Authentication solution

i use sanctum for authentication.

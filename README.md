# Bundles REST Interface

A REST interface which provide all possible combinations for broadbands.

## Requirements
```
PHP >= 5.6
```

## Run

```
composer install
composer dump-autoload -o
php -S localhost:8000
curl -X POST http://localhost:8000/list-all-broadband
```
# Multiple Cache Driver
Cache your data to Redis and Database. Get your data from Redis unless its empty. 

### Requirement
- Laravel 6.0+
- Reids
- MySQL

### Installation
```shell script
composer require 
``` 
1. require the package
1. add `CacheServiceProvider` to laravel providers in `config/app.php`.
1. change your cache driver to `multiple-driver` in `config/cache.php` like 
```php
'stores' => [
    'redis' =>[
        'driver' => 'multiple-driver',
        ...
    ],
],
```

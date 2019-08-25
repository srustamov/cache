
## Php Cache Library


<p align="center">
<a href="https://travis-ci.org/srustamov/cache"><img src="https://travis-ci.org/srustamov/cache.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/srustamov/cache"><img src="https://poser.pugx.org/srustamov/cache/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/srustamov/cache"><img src="https://poser.pugx.org/srustamov/cache/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/srustamov/cache"><img src="https://poser.pugx.org/srustamov/cache/license.svg" alt="License"></a>
</p>

```
$ composer require srustamov/cache
```

>**Default configs [srustamov/Cache/config.php]**
```php
 $cache = new Samir\Cache\Cache();
```

>**Or your config**
```php
$cacheConfig = require 'your-config-file.php';

$cache = new Samir\Cache\Cache($cacheConfig /*,$adapter| default file*/);
```

>**Switch adapter [default file]**
```php
$cache->adapter('redis');



//set value expire 10 seconds
$cache->put('key','value',10);
//Or
$cache->put('key','value')->expires(10);
//Or
$cache->expires(10)->put('key','value');

//set value expire forever
$cache->forever('key','value');



// set value expire 1 minute
$cache->put('key','value')->minutes(1);
//Or
$cache->minutes(1)->put('key','value');


// set value expire 2 hours
$cache->put('key','value')->hours(2);
//Or
$cache->hours(2)->put('key','value');



// set value expire 2 days
$cache->put('key','value')->day(2);
//Or
$cache->day(2)->put('key','value');



// set value expire 2 days
$cache->put('key','value')->day(2);
//Or
$cache->day(2)->put('key','value');


// get value
$cache->get('key');

// has key
$cache->has('key');

// forget key
$cache->forget('key');


// flush store
$cache->flush();


```

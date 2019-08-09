
##Php Cache Library

>**Default configs [srustamov/Cache/config.php]**
```
 $cache = new Samir\Cache\Cache();
```

>**Or your config**
```
$cacheConfig = require 'your-config-file.php';

$cache = new Samir\Cache\Cache($cacheConfig /*,$adapter| default file*/);
```

>**Switch adapter [default file]**
```
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

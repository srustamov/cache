<?php

require __DIR__.'/../vendor/autoload.php';

use Samir\Cache\Cache;
use Samir\Cache\Adapter\CacheAdapterInterface;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    

    public function testPut()
    {
        $cache = new Cache();

        $this->assertTrue(
            $cache->minutes(1)->put('name','cache') instanceof CacheAdapterInterface
        );
    }


    public function testGet()
    {
        $cache = new Cache();

        $this->assertEquals($cache->get('name'),'cache');
    }

   
}

<?php namespace Samir\Cache\Adapter;


use Memcached;
use RuntimeException;

class MemcacheStore implements CacheAdapterInterface
{
    private $put = false;

    private $key;

    private $expires;

    /**@var Memcached */
    private $memcache;


    public function __construct($config)
    {
        if (class_exists('\\Memcached')) {
            $this->memcache = new Memcached;
        } else {
            throw new RuntimeException("Class Memcached not found");
        }

        $this->memcache->addServer($config['host'], $config['port']);
    }


    public function put(String $key, $value, $expires = null, $forever = false)
    {
        if ($expires === null) {
            if ($this->expires === null) {
                $this->put = true;
                $this->key = $key;
                $this->memcache->set($key, $value, 10);
            } else {
                $this->memcache->set($key, $value, (int)$this->expires);
                $this->expires = null;
            }
        } else {
            $this->memcache->set($key, $value, null, $expires);

            $this->expires = null;
        }

        return $this;
    }

    public function forever(String $key, $value)
    {
        return $this->day(30)->put($key, $value);
    }

    public function has($key)
    {
        return $this->memcache->get($key) ? true : false;
    }

    public function get($key)
    {
        return $this->memcache->get($key);
    }

    public function forget($key)
    {
        return $this->memcache->delete($key);
    }

    public function expires(Int $seconds)
    {
        if ($this->put && !is_null($this->key)) {
            $this->memcache->set($this->key, $this->memcache->get($this->key), $seconds);
            $this->put = false;
            $this->key = null;
        } else {
            $this->expires = $seconds;
        }

        return $this;
    }


    public function minutes(Int $minutes)
    {
        return $this->expires($minutes * 60);
    }


    public function hours(Int $hours)
    {
        return $this->expires($hours * 3600);
    }


    public function day(Int $day)
    {
        return $this->expires($day * 3600 * 24);
    }

    public function flush()
    {
        $this->memcache->flush();
    }

    public function __get($key)
    {
        return $this->memcache->get($key);
    }


    public function __call($method, $args)
    {
        return $this->memcache->$method(...$args);
    }


    public function close()
    {
        if (method_exists($this->memcache, 'close')) {
            $this->memcache->close();
        }
    }
}

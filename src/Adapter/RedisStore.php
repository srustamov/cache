<?php namespace Samir\Cache\Adapter;


use Redis;

class RedisStore implements CacheAdapterInterface
{
    /**@var Redis*/
    private $redis;

    private $key;

    private $put;

    private $expires;


    /**
     * @param $config
     */
    public function __construct(array $config)
    {
        if ($this->redis === null) {
            $this->redis = new Redis();
            $this->redis->connect($config['host'], $config['port']);
            $this->redis->select((int) $config['database']);
            if($config['auth']['has']){
                $this->redis->auth($config['auth']['password']);
            }
        }
    }

    /**
     * @param String $key
     * @param $value
     * @param null $expires
     * @param bool $forever
     * @return $this
     */
    public function put(String $key, $value, $expires = null, $forever = false)
    {
        $this->put = true;

        $this->key = $key;

        if (is_null($expires)) {
            $expires = $this->expires;
        }

        if (is_null($expires)) {
            $this->redis->set($key, $value);
        } else {
            $this->redis->setex($key, $expires, $value);
        }

        return $this;
    }

    /**
     * @param String $key
     * @param $value
     * @return RedisStore
     */
    public function forever(String $key, $value)
    {
        return $this->day(30)->put($key, $value);
    }

    /**
     * @param $key
     * @return int
     */
    public function has($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * @param $key
     */
    public function forget($key)
    {
        $this->redis->del($key);
    }

    /**
     * @param Int $expires
     * @return $this
     */
    public function expires(Int $expires)
    {
        if (!is_null($this->put)) {
            $this->redis->expire($this->key, $expires);
        } else {
            $this->expires = $expires;
        }

        return $this;
    }


    /**
     * @param Int $minutes
     * @return RedisStore
     */
    public function minutes(Int $minutes)
    {
        return $this->expires($minutes * 60);
    }


    /**
     * @param Int $hours
     * @return RedisStore
     */
    public function hours(Int $hours)
    {
        return $this->expires($hours * 3600);
    }


    /**
     * @param Int $day
     * @return RedisStore
     */
    public function day(Int $day)
    {
        return $this->expires($day * 3600 * 24);
    }


    public function flush()
    {
        $this->redis->flushAll();
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function __get($key)
    {
        return $this->redis->get($key);
    }


    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->redis->$method(...$args);
    }

    /**
     * Close connection
     */
    public function close()
    {
        $this->redis->close();
    }
}

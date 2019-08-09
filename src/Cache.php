<?php namespace Samir\Cache;

use RuntimeException;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/cache
 */

class Cache
{
    /**@var Adapter\CacheAdapterInterface */
    private $adapter;

    public $config;


    public function __construct(array $config = [],string $adapter = null)
    {
        $this->config = !empty($config) ? $config : require __DIR__.'/config.php';

        if ($adapter !== null) {
            $this->adapter($adapter);
        } else {
            $this->adapter($this->config['adapter']);
        }
    }


    public function adapter($adapter): self
    {
        if ($adapter instanceof Adapter\CacheAdapterInterface) {
            $this->adapter = $adapter;
        } elseif (is_string($adapter)) {
            switch (strtolower($adapter)) {
                case 'file':
                    $this->adapter = new Adapter\FileStore($this->config['file']);
                    break;
                case 'memcache':
                    $this->adapter = new Adapter\MemcacheStore($this->config['memcache']);
                    break;
                case 'redis':
                    $this->adapter = new Adapter\RedisStore($this->config['redis']);
                    break;
                default:
                    throw new \RuntimeException('Cache ['.$adapter.'] adapter undefined');
                    break;
            }
        } else {
            throw new RuntimeException("Cache Adapter type undefined");
        }
        return $this;
    }


    public function put(String $key, $value, $expires = null): Adapter\CacheAdapterInterface
    {
        return $this->adapter->put($key, $value, $expires);
    }


    public function forever(String $key, $value): Adapter\CacheAdapterInterface
    {
        return $this->adapter->forever($key, $value);
    }


    public function has($key): bool
    {
        return $this->adapter->has($key);
    }


    public function get($key)
    {
        return $this->adapter->get($key);
    }


    public function forget($key): Adapter\CacheAdapterInterface
    {
        return $this->adapter->forget($key);
    }


    public function expires(Int $expires): Adapter\CacheAdapterInterface
    {
        return $this->adapter->expires($expires);
    }


    public function minutes(Int $minutes): Adapter\CacheAdapterInterface
    {
        return $this->adapter->minutes($minutes);
    }

    public function hours(Int $hours): Adapter\CacheAdapterInterface
    {
        return $this->adapter->hours($hours);
    }

    public function day(Int $day): Adapter\CacheAdapterInterface
    {
        return $this->adapter->day($day);
    }

    public function flush()
    {
        $this->adapter->flush();
    }

    public function __call($method, $args): Adapter\CacheAdapterInterface
    {
        return $this->adapter->$method(...$args);
    }


    public function __destruct()
    {
        $this->adapter->close();
    }
}

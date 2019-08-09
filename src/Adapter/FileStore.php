<?php namespace Samir\Cache\Adapter;


use Closure;
use FilesystemIterator;
use RuntimeException;

class FileStore implements CacheAdapterInterface
{
    private $path;

    private $fullPath;

    private $put = false;

    private $key;

    private $expires;


    public function __construct($config)
    {
        $this->path = $config['path'];
        if(!is_dir($config['path'])) {
            if(!mkdir($config['path'],0775,true)) {
                throw new \RuntimeException('Directory ['.$config['path'].'] could not be found or created');
            }
        }

        $this->gc();
    }


    public function put(String $key, $value, $expires = null, $forever = false)
    {
        $paths = $this->getpaths($key);

        $this->fullPath = $paths->fullPath;

        if (!$this->has($key)) {
            $this->createDir($paths);
        }

        if ($value instanceof Closure) {
            $value = call_user_func($value, $this);
        }


        if (is_null($expires)) {
            if (is_null($this->expires) && !$forever) {
                $this->put = true;

                $this->key = $key;

                file_put_contents($paths->fullPath, serialize($value));
            } else {
                file_put_contents($paths->fullPath, serialize($value));

                touch($paths->fullPath, ($forever ? -2 : (time() + $this->expires)));

                $this->expires = null;
            }
        } else {
            file_put_contents($paths->fullPath, serialize($value));

            touch($paths->fullPath, time() + $expires);

            $this->expires = null;
        }

        return $this;
    }


    public function forever(String $key, $value)
    {
        $this->put($key, $value, null, true);

        return $this;
    }


    public function has($key)
    {
        if ($key instanceof Closure) {
            $key = call_user_func($key, $this);
        }

        return $this->existsExpires($this->getpaths($key));
    }


    public function get($key)
    {
        if ($key instanceof Closure) {
            $key = call_user_func($key, $this);
        }

        $paths = $this->getpaths($key);

        if ($this->existsExpires($paths)) {
            return unserialize(file_get_contents($paths->fullPath));
        }
        return false;
    }


    public function forget($key)
    {
        if ($key instanceof Closure) {
            $key = call_user_func($key, $this);
        }

        $paths = $this->getpaths($key);

        if (file_exists($paths->fullPath)) {
            unlink($paths->fullPath);
        }

        if ($this->dirIsEmpty($this->path . '/' . $paths->path1 . '/' . $paths->path2)) {
            rmdir($this->path . '/' . $paths->path1 . '/' . $paths->path2);

            if ($this->dirIsEmpty($this->path . '/' . $paths->path1)) {
                rmdir($this->path . '/' . $paths->path1);
            }
        }
    }


    private function createDir($paths)
    {
        if (!file_exists($paths->fullPath)) {
            if (!file_exists($this->path . '/' . $paths->path1 . '/')) {
                mkdir($this->path . '/' . $paths->path1 . '/', 0755, false);
            }

            mkdir($this->path . '/' . $paths->path1 . '/' . $paths->path2 . '/', 0755, false);
        }

        return $paths->fullPath;
    }


    public function expires(Int $expires)
    {
        if ($this->put && !is_null($this->key)) {
            touch($this->fullPath, time() + $expires);

            $this->put = false;

            $this->key = null;
        } else {
            $this->expires = $expires;
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


    private function existsExpires($paths)
    {
        if (file_exists($paths->fullPath)) {
            $mtime = filemtime($paths->fullPath);

            if ($mtime <= time() && $mtime > 0) {
                unlink($paths->fullPath);

                if ($this->dirIsEmpty($this->path . '/' . $paths->path1 . '/' . $paths->path2)) {
                    rmdir($this->path . '/' . $paths->path1 . '/' . $paths->path2);

                    if ($this->dirIsEmpty($this->path . '/' . $paths->path1)) {
                        rmdir($this->path . '/' . $paths->path1);
                    }
                }
                return false;
            }
            return true;
        }
        return false;
    }


    private function getPaths($key)
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

        $fullPath = $this->path . '/' . $parts[0] . '/' . $parts[1] . '/' . $hash;

        return (object)array('path1' => $parts[0], 'path2' => $parts[1], 'fullPath' => $fullPath);
    }

    public function dirIsEmpty($dir): bool
    {
        $iterator = new FilesystemIterator($dir);

        return !$iterator->valid();
    }


    public function flush()
    {
        $this->flushDir($this->path);
    }


    private function flushDir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->flushDir($file);

                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }


    public function gc()
    {
        $directoryRead = glob($this->path . "/*");

        foreach ($directoryRead as $file) {
            $mtime = filemtime($file);

            if (is_file($file) && $mtime <= time() && $mtime > 0) {
                unlink($file);
            }
        }

        foreach ($directoryRead as $dir) {
            if (is_dir($dir) && $this->dirIsEmpty($dir)) {
                rmdir($dir);
            }
        }
    }


    public function __get($key)
    {
        return $this->get($key);
    }


    public function __call($method, $args)
    {
        throw new RuntimeException("Call to undefined method Cache::$method()");
    }


    public function close()
    {
        return true;
    }
}

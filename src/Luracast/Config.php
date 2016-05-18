<?php namespace Luracast\Config;

use ArrayAccess;

/**
 * Config class for loading configuration arrays from various files and provide easy
 * access to nested properties with dot syntax
 *
 * Lazy loads configuration information when requested using Config::get('file.property')
 * or $config['file.property']
 *
 * For example
 *
 * Config::get['database.connections.sqlite'] will load database.php
 * which returns an array that contains connections property
 * which contains the sqlite property value of which will be returned.
 *
 * $path given in the constructor is the path it will look for the file
 *
 * When an environment string is specified, it will look for a folder
 * with that name inside the path and use the returned array to override
 * the properties is original config file thus allowing some customization
 *
 * @package Luracast\Config
 */
class Config implements ArrayAccess
{
    protected $container = array();
    /** @var  string */
    protected $path;
    /** @var  string */
    protected $environment;

    /** @var  static */
    protected static $instance;

    public function __construct($path, $environment = null)
    {
        $this->path = $path;
        $this->environment = $environment;
        if (!static::$instance) {
            static::$instance = $this;
        }
    }

    /**
     * Initialize the Config instance for a specific target path
     *
     * @param string $path folder path for the config files
     * @param string|null $environment path for fine tuning config files with overriding properties
     *
     * @return Config
     */
    public static function init($path, $environment = null)
    {
        if (!static::$instance)
            static::$instance = new Config($path, $environment);
        else {
            static::$instance->path = $path;
            static::$instance->environment = $environment;
            static::$instance->container = array();
        }

        return static::$instance;
    }

    public static function get($name, $default = null)
    {
        if (!static::$instance)
            throw new \BadFunctionCallException('Config::init($path, $environment) should to be called first');

        return static::$instance->offsetGet($name) ?: $default;
    }

    public static function set($key, $value)
    {
        if (!static::$instance)
            throw new \BadFunctionCallException('Config::init($path, $environment) should to be called first');
        $instance = static::$instance;
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                static::arraySet($instance->container, $innerKey, $innerValue);
            }
        } else {
            static::arraySet($instance->container, $key, $value);
        }
    }

    public function offsetExists($offset)
    {
        if (isset($this->container[$offset])) {
            return true;
        }
        $name = strtok($offset, '.');
        if (isset($this->container[$name])) {
            $p = $this->container[$name];
            while (false !== ($name = strtok('.'))) {
                if (!isset($p[$name]))
                    return false;
                $p = $p[$name];
            }
            $this->container[$offset] = $p;

            return true;
        } else {
            //lazy load the config file
            if (is_readable("$this->path/$name.php")) {
                //merge environment file if available
                $this->container[$name] = include "$this->path/$name.php";
                if (!empty($this->environment) && is_readable($file = "$this->path/$this->environment/$name.php")) {
                    $this->container[$name] = array_replace_recursive($this->container[$name], (include $file));
                }

                return $this->offsetExists($offset);
            }
        }

        return false;
    }


    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->container[$offset] : null;
    }


    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }


    public function offsetUnset($offset)
    {
        $this->container[$offset] = null;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     *
     * @return array
     */
    private static function arraySet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}
<?php
namespace Initially\Service;

/**
 * Class DependencyInjection
 *
 * @package Initially\Service
 */
class DependencyInjection
{

    /**
     * @var array
     */
    private static $container = array();

    /**
     * @param string $key
     * @return null|mixed
     */
    public static function get($key)
    {
        $key = trim($key);
        if (empty($key) || !isset(self::$container[$key])) {
            return null;
        } elseif (is_callable(self::$container[$key])) {
            self::$container[$key] = call_user_func(self::$container[$key]);
        }
        return self::$container[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $key = trim($key);
        if (!empty($key)) {
            self::$container[$key] = $value;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        $key = trim($key);
        if (!empty($key)) {
            return isset(self::$container[$key]);
        }
        return false;
    }

}
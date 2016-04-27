<?php
namespace Initially\Service;

class Config
{

    /**
     * @var array
     */
    private $container = array();

    /**
     * @var string
     */
    private static $ext = "php";

    /**
     * Config constructor.
     * @param array $files
     */
    private function __construct(array $files)
    {
        $this->_load($files);
    }

    /**
     * @param array $files
     */
    private function _load(array $files)
    {
        foreach ($files as $file) {
            $key = basename($file, "." . self::$ext);
            $config = include "{$file}";
            if (isset($this->container[$key])) {
                $this->container[$key] = array_merge($this->container[$key], $config);
            } else {
                $this->container[$key] = $config;
            }
        }
    }

    /**
     * @param array $dirs
     * @return Config
     * @throws AppException
     */
    public static function factory(array $dirs)
    {
        if (empty($dirs)) {
            throw new AppException("config directories error");
        }

        $files = array();
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                throw new AppException("config directory \"{$dir}\" not exists");
            }
            $files = array_merge($files, glob("{$dir}/*." . self::$ext));
        }

        return $config = new Config($files);
    }

    /**
     * Get config
     *
     * @param string $key
     * @return array
     */
    public function get($key = "")
    {
        if (empty($key)) {
            return $this->container;
        }

        $keyArr = strpos($key, ".") !== false ? explode(".", $key) : array($key);
        $container = $this->container;
        foreach ($keyArr as $key) {
            if ($key == "" || !isset($container[$key])) {
                $container = null;
                break;
            } else {
                $container = $container[$key];
            }
        }

        return $container;
    }

}
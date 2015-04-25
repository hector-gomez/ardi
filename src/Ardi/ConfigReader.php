<?php

namespace Ardi;


/**
 * Reads the configuration file and exposes a method to read from it.
 *
 * @package Ardi
 */
class ConfigReader
{
    private static $instances = array();

    // Path to the folder where the configuration files are
    private static $configDir = 'config';

    private $cachedConfig;
    private $cachedKeys = array();

    /**
     * Obtain an instance of this class that is shared by all classes.
     *
     * @param string $configFile Name of the ini file to load (omit the extension)
     * @return ConfigReader Shared instance
     */
    public static function getReader($configFile = 'app')
    {
        if (!array_key_exists($configFile, self::$instances) || is_null(self::$instances[$configFile])) {
            self::$instances[$configFile] = new self($configFile);
        }
        return self::$instances[$configFile];
    }

    /**
     * Initialises the instance parsing the configuration file
     *
     * @param string $iniFilename Name of the ini file to load (omit the extension)
     */
    private function __construct($iniFilename)
    {
        $this->cachedConfig = parse_ini_file(self::$configDir.'/'.$iniFilename.'.ini', true);
    }

    /**
     * Retrieve a stored configuration. If the required value has sub-values, an array will be returned.
     * It is also possible to specify which of the child values will be returned spacing them with dots (.)
     *
     * @param string $key Name of the value to return, or a dot-separated path to it
     * @return string|array|bool False if the key does not exist
     */
    public function get($key)
    {
        if (!isset($this->cachedKeys[$key])) {
            $path = explode('.', $key);
            $current = $this->cachedConfig;
            for ($i = 0; $i < count($path); $i++) {
                $index = $path[$i];
                if (!array_key_exists($index, $current)) {
                    return false;
                }
                $current = $current[$index];
            }
            $this->cachedKeys[$key] = $current;
        }
        return $this->cachedKeys[$key];
    }

    /**
     * @return string Path to the folder that contains all configuration files
     */
    public static function getConfigDir()
    {
        return self::$configDir;
    }

    /**
     * Sets the folder that contains all configuration files
     *
     * @param string $configDir Path to the folder that contains all the configuration files
     */
    public static function setConfigDir($configDir)
    {
        self::$configDir = $configDir;
    }
}

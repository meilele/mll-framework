<?php

namespace Mll\Core;

use Mll\Common\Dir;

class Config
{

    /**
     * 配置文件
     * @var array
     */
    private static $config;


    /**
     * load.
     *
     * @param $configPathArr
     *
     * @return array
     */
    public static function load(array $configPathArr)
    {
        $config = array();
        if (is_array($configPathArr)) {
            foreach ($configPathArr as $configPath) {
                $files = Dir::tree($configPath, '/.php$/');
                array_map(function ($file) use (&$config) {
                    $config += include "{$file}";
                }, $files);
            }
        }
        self::$config = $config;

        return self::$config;
    }

    /**
     * 合并配置文件
     *
     * @param $file
     * @return bool
     */
    public static function mergeFile($file)
    {
        $tmp = include "{$file}";
        if (empty($tmp)) {
            return false;
        }
        self::$config = array_merge(self::$config, $tmp);
        return true;
    }


    /**
     * 获取配置
     *
     * @param $key
     * @param null $default
     * @param bool $throw
     * @return mixed|null
     * @throws \Exception
     */
    public static function get($key, $default = null, $throw = false)
    {
        $key = '["' . implode('"]["', explode('.', $key)) . '"]';
        $varStr = 'self::$config' . $key;
        $result = eval("return isset($varStr) ? $varStr : \$default;");
        if ($throw && is_null($result)) {
            throw new \Exception("{key} config empty");
        }
        return $result;
    }

    /**
     * 设置配置
     *
     * @param $key
     * @param $value
     * @param bool $set
     * @return bool
     */
    public static function set($key, $value, $set = true)
    {
        if ($set) {
            self::$config[$key] = $value;
        } else {
            if (empty(self::$config[$key])) {
                self::$config[$key] = $value;
            }
        }

        return true;
    }

    /**
     * 获取配置字段
     *
     * @param $key
     * @param $field
     * @param null $default
     * @param bool $throw
     * @return null
     * @throws \Exception
     */
    public static function getField($key, $field, $default = null, $throw = false)
    {
        $result = isset(self::$config[$key][$field]) ? self::$config[$key][$field] : $default;
        if ($throw && is_null($result)) {
            throw new \Exception("{key} config empty");
        }
        return $result;
    }

    /**
     * 设置配置字段
     *
     * @param $key
     * @param $field
     * @param $value
     * @param bool $set
     * @return bool
     */
    public static function setField($key, $field, $value, $set = true)
    {
        if ($set) {
            self::$config[$key][$field] = $value;
        } else {
            if (empty(self::$config[$key][$field])) {
                self::$config[$key][$field] = $value;
            }
        }

        return true;
    }

    /**
     * 获取所有配置
     *
     * @return array
     */
    public static function all()
    {
        return self::$config;
    }
}
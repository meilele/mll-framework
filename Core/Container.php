<?php

namespace Mll\Core;

/**
 * 服务容器（简化版的 service Locator服务定位器 和 DI依赖注入）
 *
 * @author Dzer <dz@mnapoli.fr>
 */
class Container
{
    /**
     * 依赖的定义
     * @var array
     */
    private static $definitions = [];

    /**
     * 对象
     * @var array
     */
    private static $instances = [];

    /**
     * 别名与实例关系
     * @var array
     */
    private static $classAlias = [];

    /**
     * 依赖关系
     * @var array
     */
    //private static $dependencies = [];

    /**
     * 依赖信息
     * @var array
     */
    //private $reflections = [];

    /**
     * 实例化（单例）
     *
     * @param $className
     * @param null $params
     * @return mixed
     * @throws \Exception
     */
    public static function getInstance($className, $params = null)
    {
        $keyName = $className;
        if (!empty($params['_prefix'])) {
            $keyName .= $params['_prefix'];
        }
        if (isset(self::$instances[$keyName])) {
            return self::$instances[$keyName];
        }

        if (!class_exists($className)) {
            throw new \Exception("no class {$className}");
        }
        if (empty($params)) {
            self::$instances[$keyName] = new $className();
        } else {
            self::$instances[$keyName] = new $className($params);
        }
        return self::$instances[$keyName];
    }

    /**
     * 添加服务配置文件
     *
     * @param array $definitions
     * @return array
     */
    public static function addDefinitions(array $definitions)
    {

        return self::$definitions += $definitions;
    }

    /**
     * 获取实例
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public static function get($name)
    {
        if (isset(self::$classAlias[$name]) && isset(self::$instances[self::$classAlias[$name]])
            && is_object(self::$instances[self::$classAlias[$name]])
        ) {
            return self::$instances[self::$classAlias[$name]];
        }

        if (isset(self::$definitions[$name]) && is_callable(self::$definitions[$name])) {
            $callable = self::$definitions[$name];
            self::$classAlias[$name] = get_class($callable());
            return self::$instances[self::$classAlias[$name]];
        }
        throw new \Exception("No entry or class found for '$name'");
    }

    public static function getInstances()
    {
        return self::$instances;
    }

    /**
     * 设置容器变量
     *
     * @param string $name 别名
     * @param string|array|object|callable $value 值可以是类名、实例、可调用结构、数组
     * @param bool $is_cover 是否覆盖
     * @return bool
     * @throws \Exception
     */
    /*public function set($name, $value, $is_cover = true)
    {
        // todo 设置容器变量

    }*/

    /**
     * 设置容器变量
     *
     * @param string $name 别名
     * @param string|array|object|callable $definition 值可以是类名、实例、可调用结构、数组
     * @return mixed
     * @throws \Exception
     */
    /* protected static function normalizeDefinition($name, $definition)
     {

     }*/

    /**
     * 测试名称是否存在容器中
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset(self::$instances[self::$classAlias[$name]])) {
            return true;
        }
        return false;
    }

    /**
     * 创建一个实例
     * 暂时只做一层
     *
     * @param $name
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    /*public function make($name, array $parameters = [])
    {

    }*/

    /*public function call($callable, array $parameters = [])
    {

    }*/

    /**
     * 分析依赖关系
     *
     * @param $class
     * @return array
     */
    /*protected function getDependencies($class)
    {

    }*/
}

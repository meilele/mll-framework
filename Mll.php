<?php

namespace Mll;

use Mll\Config;
use Mll\Exception\Error;
use Mll\Core\Container;

/**
 * Class Mll
 *
 * @package Mll
 * @property \Mll\Config\Driver\ArrayFormat $config
 * @property \Mll\Request\Base $request
 * @property \Mll\Log\ILog $log
 * @property \Mll\Server\IServer $server
 * @property \Mll\Rpc\IRpc $rpc
 * @property \Mll\Session $session
 * @property \Mll\Cache $cache
 * @property \Mll\View\Base $view
 * @author Xu Dong <d20053140@gmail.com>
 * @since 1.0
 */
class Mll
{
    /**
     * debug模式
     * @var
     */
    public static $debug;

    /**
     * serverModel
     * @var
     */
    public static $serveModel;

    /**
     * 加载类.
     *
     * @var array
     */
    private static $classMap = [
        'Mll\\Core\\Container' => '/Core/Container.php',
        'Mll\\Config\\Factory' => '/Config/Factory.php',
        'Mll\\Config\\Driver\\ArrayFormat' => '/Config/Driver/ArrayFormat.php',
        'Mll\\Config\\IConfig' => '/Config/IConfig.php',
        'Mll\\Common\\Dir' => '/Common/Dir.php',
        'Mll\\Exception\\Error' => '/Exception/Error.php',
        'Mll\\Server\\Factory' => '/Server/Factory.php',
        'Mll\\Server\\Driver\\Http' => '/Server/Driver/Http.php',
        'Mll\\Server\\IServer' => '/Server/IServer.php',
        'Mll\\Request\\Factory' => '/Request/Factory.php',
        'Mll\\Request\\Driver\\Http' => '/Request/Driver/Http.php',
        'Mll\\Request\\Base' => '/Request/Base.php',
        'Mll\\Request\\IRequest' => '/Request/IRequest.php',
        'Mll\\Core\\Route' => '/Core/Route.php',
        'Mll\\Controller' => '/Controller.php',
        'Mll\\Controller\\IController' => '/Controller/IController.php',
        'Mll\\Response\\Response' => '/Response/Response.php',
        'Mll\\Response\\Driver\\Json' => '/Response/Driver/Json.php',
        'Mll\\Common\\Common' => '/Common/Common.php',
        'Mll\\Log\\Driver\\File' => '/Log/Driver/File.php',
        'Mll\\Log\\Driver\\Cache' => '/Log/Driver/Cache.php',
        'Mll\\Log\\Base' => '/Log/Base.php',
        'Mll\\Log\\ILog' => '/Log/ILog.php',
        'Mll\\Log\\Factory' => '/Log/Factory.php',
    ];

    public function __get($name)
    {
        return Container::get($name);
    }

    /**
     * app
     *
     * @return self
     */
    public static function app()
    {
        return Container::getInstance(__CLASS__);
    }

    /**
     * run
     *
     * @param string $serveModel
     */
    public function run($serveModel = 'Http')
    {
        self::$serveModel = $serveModel;
        //自动加载
        spl_autoload_register(__CLASS__ . '::autoload', true, true);

        //先将配置组件加入容器
        Container::addDefinitions([
            'config' => function () {
                return Config\Factory::getInstance();
            },
        ]);

        //加载公共配置文件
        Mll::app()->config->loadFile(__DIR__ . '/config.php');
        Mll::app()->config->load(self::getConfigPath());

        //服务容器
        Container::addDefinitions(Mll::app()->config->get('container'));

        //时区设置
        date_default_timezone_set(Mll::app()->config->get('time_zone', 'Asia/Shanghai'));

        //设置调试模式
        self::$debug = Mll::app()->config->get('app_debug', true);

        //xhprof
        self::Xhprof();

        //错误注册
        Error::register();

        //run server
        Mll::app()->server->run();
    }

    /**
     * 获取配置文件目录路径.
     *
     * @param null $module
     *
     * @return array
     */
    public static function getConfigPath($module = null)
    {
        $path = [];
        if (!empty($module)) {
            $moduleConfigPath = ROOT_PATH . DS . 'app' . DS . $module . DS . 'config';
            if (is_dir($moduleConfigPath)) {
                $path[] = $moduleConfigPath;
            }
        } else {
            $path[] = ROOT_PATH . DS . 'app' . DS . 'common' . DS . 'config';
        }

        return $path;
    }

    /**
     * 自动加载.
     *
     * @param $className
     *
     * @throws \Exception
     */
    public static function autoload($className)
    {
        if (isset(static::$classMap[$className])) {
            $classFile = MLL_PATH . static::$classMap[$className];
        } elseif (strpos($className, '\\') !== false) {
            $classFile = ROOT_PATH . DS . str_replace('\\', DS, $className) . '.php';
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        include $classFile;

        if (self::$debug && !class_exists($className, false) && !interface_exists($className, false)
            && !trait_exists($className, false)
        ) {
            throw new \Exception("没有找到 '$className'：$classFile");
        }
    }

    /**
     * Xhprof 性能分析
     */
    public static function Xhprof()
    {
        if (Mll::app()->config->get('xhprof.enable', false)
            && function_exists('xhprof_enable')
        ) {
            xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS);
        }
    }
}

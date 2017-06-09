<?php

namespace Mll\Exception;

use Mll\Mll;

class Error
{
    /**
     * 注册异常处理
     * @return void
     */
    public static function register()
    {
        //error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * Exception Handler
     * @param  \Exception|\Throwable $e
     */
    public static function appException($e)
    {
        if (!$e instanceof \Exception) {
            $e = new ThrowableError($e);
        }

        self::getExceptionHandler()->report($e);
        //self::getExceptionHandler()->render($e)->send();
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        $data['echo'] = ob_get_clean();

       /* ob_start();
        extract($data);
        include Config::get('exception_tmpl');
        // 获取并清空缓存
        $content  = ob_get_clean();*/
        ob_start();
        $outDate = self::getExceptionHandler()->render($e);

        if (MLL_ENV_PROD) {
            echo $outDate['message'];
        } else {
            if($_SERVER['CONTENT_TYPE'] == 'application/json')  {
                echo json_encode($outDate);
            } else {
                echo json_encode($outDate);
            }
        }
        ob_end_flush();
    }

    /**
     * Error Handler
     * @param  integer $errno 错误编号
     * @param  integer $errstr 详细错误信息
     * @param  string $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @param array $errcontext
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline, $errcontext);
        if (error_reporting() & $errno) {
            // 将错误信息托管至 think\exception\ErrorException
            throw $exception;
        } else {
            self::getExceptionHandler()->report($exception);
        }
    }

    /**
     * Shutdown Handler
     */
    public static function appShutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            // 将错误信息托管至think\ErrorException
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            self::appException($exception);
        }
        // 写入日志
        Mll::app()->log->save();
    }

    /**
     * 确定错误类型是否致命
     *
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @return Handle
     */
    public static function getExceptionHandler()
    {
        static $handle;
        if (!$handle) {
            // 异常处理handle
            $class = Mll::app()->config->get('exception.exception_handle');
            if ($class && class_exists($class) && is_subclass_of($class, "\\Mll\\Exception\\Handle")) {
                $handle = new $class;
            } else {
                $handle = new Handle;
            }
        }
        return $handle;
    }
}

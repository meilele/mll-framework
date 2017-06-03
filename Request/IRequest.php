<?php

namespace Mll\Request;

interface IRequest
{
    /**
     * 将不同server的传输数据统一格式.
     *
     * @param $request
     *
     * @return mixed
     */
    public static function parse($request);

    /**
     * 获取请求参数.
     *
     * @return mixed
     */
    public static function getParams();

    /**
     * 设置请求参数.
     */
    public static function setParams();

    /**
     * 获取模块.
     *
     * @return mixed
     */
    public static function getModule();

    /**
     * 获取控制器.
     *
     * @return mixed
     */
    public static function getController();

    /**
     * 获取方法.
     *
     * @return mixed
     */
    public static function getMethod();
}
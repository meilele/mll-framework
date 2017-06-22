<?php

namespace Mll\Common;

class ReturnMsg
{
    const RIGHT_CODE = 0;
    const ERROR_CODE = 1;
    /**
     * 返回正确结果.
     *
     * @param string $msg
     *
     * @return array
     */
    public static function ret($msg){
        return [
            'error' => self::RIGHT_CODE,
            'msg' => $msg
        ];
    }
    /**
     * 返回错误结果.
     *
     * @param string $msg
     * @param int $errorCode
     *
     * @return array
     */
    public static function err($msg,$errorCode = self::ERROR_CODE){
        return [
            'error' => $errorCode,
            'msg' => $msg,
        ];
    }
}

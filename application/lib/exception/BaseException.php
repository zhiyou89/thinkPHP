<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 12:02
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    public $code = 400;
    public $msg = "参数错误";
    public $errorCode = 10000;
}
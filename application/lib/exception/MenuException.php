<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 20:48
 */

namespace app\lib\exception;


class MenuException extends BaseException
{
    public $code = 404;
    public $msg = '菜谱不存在，检查参数';
    public $errorCode = 40000;
}
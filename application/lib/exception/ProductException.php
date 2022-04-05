<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 22:30
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $msg = "商品不存在，检查参数";
    public $errorCode = 20000;
}
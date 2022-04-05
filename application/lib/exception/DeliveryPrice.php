<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 23:10
 */

namespace app\lib\exception;


class DeliveryPrice extends BaseException
{
    public $code = 404;
    public $msg = "订单配送费异常";
    public $errorCode = 70000;
}
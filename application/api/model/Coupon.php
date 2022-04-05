<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:44
 */

namespace app\api\model;


class Coupon extends BaseModel
{
    protected function getUpTimeAttr($value){
        return date('Y.m.d',$value);
    }
    protected function getDownTimeAttr($value){
        return date('Y.m.d',$value);
    }

    public static function moneyByCoupon($couponID){
       $res = self::get($couponID);
       return $res->money;
    }
}
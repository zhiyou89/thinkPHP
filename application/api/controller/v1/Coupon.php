<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 22:57
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Coupon as couponModel;


class Coupon extends BaseController
{

    //获取优惠幅度
    public function getCouponMoney(){
        $couponID  = input('id');
        return couponModel::moneyByCoupon($couponID);
    }
    public function getCouponList(){
        return couponModel::select()->toArray();
    }
}
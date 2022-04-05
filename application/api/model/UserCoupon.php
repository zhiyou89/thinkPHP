<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:44
 */

namespace app\api\model;


class UserCoupon extends BaseModel
{
    public static function saveData($userID,$couponID){
       return self::create([
            'user_id'=>$userID,
            'coupon_id'=>$couponID,
            'create_time'=>time()
        ]);
    }

    public static function usedChangeCouponStatus($id){
        return self::update([
            'id'=>$id,
            'show'=>0
        ]);
    }
}
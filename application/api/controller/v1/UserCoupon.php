<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 22:57
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\UserCoupon as UserCouponModel;

class UserCoupon extends BaseController
{
    public function updateStatus(){
        $id = input('id');
       $res = UserCouponModel::usedChangeCouponStatus($id);
       if($res){
           return json([
               'errorCode'=>200,
               'msg'=>'success'
           ]);
       }
    }

}
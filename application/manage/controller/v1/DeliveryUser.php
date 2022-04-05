<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 21:48
 */

namespace app\manage\controller\v1;

use think\Controller;

use app\manage\model\DeliveryUser as DeliveryUserModel;
use my\Redis as RedisService;

class DeliveryUser extends Controller
{
    public function LogIN(){
        $res = DeliveryUserModel::LogIN();

        if($res){
            return [
                'code'=>200,
                'msg'=>'登录成功',

            ];
        }else{
            return [
                'code'=>400,
                'msg'=>'登录失败',

            ];
        }
    }
}
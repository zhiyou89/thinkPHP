<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\server\AccessToken as AccessTokenservice;
use app\api\server\Token;
use my\Redis as RedisService;
use app\api\model\User as UserModel;


class WxCode extends BaseController
{
    public function getWxCode(){

        $userID = Token::getCurrentUid();
        $user = UserModel::get($userID);
        if(!$user->ew_code){
            $redis = new RedisService();
            UserModel::update([
                'id'=> $userID,
                'ew_code'=>input('code')
            ]);
//            $key = 'u_'.$userID;
//            $redis->set($key,input('code'));
        }
        return [
            'msg'=>'操作成功'
        ];
    }

    public function getRediesCode(){
        $userID = Token::getCurrentUid();
        $user = UserModel::get($userID);
        return $user->ew_code;
//        $redis = new RedisService();
//        $key = 'u_'.$userID;
//        return $redis->get($key);
    }
}
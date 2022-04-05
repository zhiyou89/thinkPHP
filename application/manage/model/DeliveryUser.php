<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/4
 * Time: 22:27
 */
namespace app\manage\model;

use think\Model;
use my\Redis as RedisService;

class DeliveryUser extends Model
{
    public static function LogIN(){
        $name = input('name');
        $psw = input('password');
        $res = self::where('delivery_name','=',$name)->find();
        if(!empty($res)){
           if($res->password === md5($psw)){
               return 1;
           }else{
               return 0;
           }
        }
    }

    public static function getUserID($userName){
        $res = self::where('delivery_name',$userName)->find();
        return $res->id;
    }
}
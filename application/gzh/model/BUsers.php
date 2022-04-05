<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\model;
use think\Model;
class BUsers extends Model
{
   public static function logIn($info){
       if($info){
           $openid = $info->openid;
           $user = self::where('openid',$openid)
               ->find();
           if(empty($user)){
              $res = self::create([
                   'openid'=>$openid,
                   'nick_name'=>serialize($info->nickname),
                   'head_img_url'=>$info->headimgurl,
                   'create_time'=>time()
               ]);
              return 11;
//               return self::setToken($res->id,$openid);
           }
       }
   }

   public static function setToken($id,$openid){
       $arr = [];
       $salt = 'askdhfjsadfha';
       $k = md5(md5($openid.$salt));
       $arr = [
           'id' =>$id,
           'openid'=>$openid
       ];
       cache($k,$arr,7200);
       return $k;
   }
}
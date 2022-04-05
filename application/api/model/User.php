<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:44
 */

namespace app\api\model;
use app\api\server\Token;
use app\api\model\UserCoupon;

class User extends BaseModel
{
    protected $hidden = ['pivot','delete_time','create_time'];
    public function userCoupon(){
        return  $this->hasMany('user_coupon','user_id','id');
    }
    public static function getCouponByUserID($id){
       $res = self::with([
            'userCoupon' =>function($query){
                $query->with('coupon')->where('show',1);
            }
        ])
        ->where('id',$id)
        ->find();
       return $res;
    }
//    protected function getNickNameAttr($value,$data){
//       if(!$data['update_time']){
//           $value = '游客_'.$value;
//       }
//        return $value;
//    }
    public static function getByOpenID($openid){
        $where = [];
        $where['openid'] = $openid;
        return self::get($where);
    }
    //判断今天是否领取过
    public static function haveGetCoupon(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $time['create_time'] = array('between',array($beginToday,$endToday));
        $userID = Token::getCurrentUid();
        $userCouponInfo = UserCoupon::where('user_id',$userID)
            ->where($time)->find();
        if(!empty($userCouponInfo)){
            return [
                'errorCode'=>202,
                'msg'=>'您今天已经领取过了'
            ];
        }else{
            return [
                'errorCode'=>200,
                'msg'=>'可以领取优惠券'
            ];
        }
    }

    public static function upFrieds($UserId){
        $res = self::where('id',$UserId)->find();
        return $res->pid;
    }

}
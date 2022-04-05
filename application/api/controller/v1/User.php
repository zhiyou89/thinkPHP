<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\api\controller\v1;



use app\api\server\Token;
use app\api\controller\BaseController;
use app\api\model\User as userModel;
use app\api\model\UserCoupon;
use app\api\server\User as UserService;
use app\api\server\WxPhone as WxPhoneService;

class User extends BaseController
{
    public function getUserInfo(){
        $userID = Token::getCurrentUid();

        return  userModel::get($userID);
    }

    public function editUserInfo(){
        $id = Token::getCurrentUid();
        if($id){
            $user = userModel::update(
                [
                    'id'=>$id,
                    'nickname'=>input('nickname'),
                    'header_img'=>input('header_img'),
                    'update_time'=>time()
                ]);
            return $user->id;
        }else{
            return 0;
        }

    }

    //根据用户ID获取该用户下的所有优惠券
    public function getCoupon(){
        $userID = Token::getCurrentUid();
      return  userModel::getCouponByUserID($userID);
    }



    //给用户发放优惠券

    public function giveCouponToUser(){
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
            $couponID = input('id');

            UserCoupon::create([
                'user_id'=>$userID,
                'coupon_id'=>$couponID,
                'create_time'=>time()
            ]);
            return [
                'errorCode'=>200,
                'msg'=>'减免配送费优惠券已发送到您的券包'
            ];
        }


    }

    public function haveGetCoupon(){
        return userModel::haveGetCoupon();
    }

    public function usersTree(){
        $id = Token::getCurrentUid();
        $UserService = new UserService();
        return $UserService->tree($id);
    }

    public function saveSuperiorID(){
        $msg = '';
        $id = Token::getCurrentUid();
        $pid = input('pid');
        if($id == $pid){
            return [
                'msg'=>'您不能自己成为自己的上级'
            ];
        }
        $user = userModel::get($id);
        if(!$user->pid){
            userModel::update([
                'id'=>$id,
                'pid'=>$pid
            ]);
            $msg = '恭喜您，加入团队成功';
        }else{
            $msg = '您已经加入团队了';
        }
        return [
            'msg'=>$msg
        ];

    }

    public function havePhone(){
        $id = Token::getCurrentUid();
        $res = userModel::get($id);
        if($res->phone){
            return [
                'msg'=>'success'
            ];
        }else{
            return [
                'msg'=>'fail'
            ];
        }
    }

    /**
     * 写入个人中心的密码
     */
    public function updatePasswordForPersonCenter(){
        $id = Token::getCurrentUid();
        $password = input('password');
        $res = userModel::update([
            'id'=>$id,
            'password'=>$password
        ]);
        if($res){
            return [
                'msg'=>'success'
            ];
        }
    }

    /**
     * @return mixed数据库中获取指定ID的数据
     */
    public function getTelephoneBySql(){
        $id = Token::getCurrentUid();
        $res = userModel::get($id);
        return $res;
    }

    /**
     * @return \think\response\Json微信中获取手机信息
     */
    public function getTelephone(){
        $UserService = new WxPhoneService();
        $data =  $UserService->wxTelephone();
        return json($data);
    }

    /**
     * @return array获取到的手机信息写入数据库
     */
    public function updataPhone(){
        $id = Token::getCurrentUid();
        $phone = input('phone');
        $res = userModel::update([
            'id'=>$id,
            'phone'=>$phone
        ]);
        if($res){
            return [
                'msg'=>'success'
            ];
        }

    }
}
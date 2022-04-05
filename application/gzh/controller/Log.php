<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\controller;
use think\Controller;
use app\gzh\model\BUsers as BUsersModel;

class Log extends Controller
{
    public function logIn(){
        $phone = input('phone');
        $psw = md5(input('password'));
        $res = BUsersModel::where('phone',$phone)
            ->where('psw',$psw)
            ->find();
        if(!empty($res)){
            return [
                'code'=>200,
                'msg'=>'登录成功'
            ];
        }else{
            return [
                'code'=>400,
                'msg'=>'登录失败'
            ];
        }
    }
}
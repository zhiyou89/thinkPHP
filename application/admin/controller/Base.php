<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller;



use think\Controller;

class Base extends Controller
{
    public function returnJson($res){
        if(!$res->isEmpty()){
            return [
                'status'=>200,
                'msg'=>'success',
                'data'=>$res->toArray()
            ];
        }else{
            return [
                'status'=>500,
                'msg'=>'service error',
                'data'=>[]
            ];
        }
    }
}

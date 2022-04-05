<?php

namespace app\api\model;
use think\Cache;
use my\Redis as RedisService;
use app\api\model\BProduct;
use app\api\model\BOrder;
use app\api\model\BUsers;
class BAddress extends BaseModel
{

    public static function setAddress($address){
        $arr = [];
        $userID = BUsers::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        };
        $key ='address'.$userID;
        $addressInCache = Cache::get($key);
//        Cache::set($key,json_encode([]),0);die;
        $addressArr = BUsers::changeArr($addressInCache);
        array_unshift($addressArr,$address);

        Cache::set($key,json_encode($addressArr),0);
        return [
            'code'=>200,
            'msg'=>'添加地址成功'
        ];
    }

    public static function getAddress(){
        $addressArr = [];
        $userID = BUsers::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        };
        $key ='address'.$userID;
        $addressInCache = Cache::get($key);
//        $addressInCache = Cache::set($key,'');
//        print_r($addressInCache);die;
        $addressArr = BUsers::changeArr($addressInCache);

        return [
            'code'=>200,
            'msg'=>'获取数据成功',
            'data'=>$addressArr
        ];
    }


    public static function getOneAddress($id){
        $res = self::getAddress();
        if($res['code'] == 200){
            $arr = $res['data'];
            foreach ($arr as $v){
                if($v['id'] == $id){
                    return $v;
                }
            }
        }

    }

    public static function editAddress($address,$id){
        $res = self::setAddress($address);
        if($res['code'] == 200){
            $result = self::delAddress($id);
            if($result['code'] == 200){
                return [
                    'code'=>200,
                    'msg'=>'修改地址成功'
                ];
            }
        }

    }

    public static function delAddress($id){
        $arr = [];
        $userID = BUsers::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        };
        $key ='address'.$userID;
        $addressInCache = Cache::get($key);
        $addressArr = BUsers::changeArr($addressInCache);
        foreach ($addressArr as $v){
            if($v['id'] != $id){
                array_push($arr, $v);
            }
        }
        Cache::set($key,json_encode($arr),0);
        return [
            'code'=>200,
            'msg'=>'删除数据成功'
        ];

    }

    public static function chooseAddress($id){
        $arr = [];
        $address = [];
        $userID = BUsers::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        };
        $key ='address'.$userID;
        $addressInCache = Cache::get($key);
        $addressArr = BUsers::changeArr($addressInCache);
        foreach ($addressArr as $v){
            if($v['id'] != $id){
                array_push($arr, $v);
            }else{
                $address = $v;
            }
        }

        array_unshift($arr,$address);
        Cache::set($key,json_encode($arr),0);
        return [
            'code'=>200,
            'msg'=>'地址选择成功'
        ];
    }

}
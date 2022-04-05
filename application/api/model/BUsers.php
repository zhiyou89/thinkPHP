<?php

namespace app\api\model;
use think\Cache;
use my\Redis as RedisService;
use app\api\model\BProduct;
use app\api\model\BOrder;

class BUsers extends BaseModel
{
    /**
     * @param $token
     * @param $id
     * @return bool
     * 加如购物车
     */
    public static function saveCart($id){
        $have = 0;
        $userID = self::getUserId();
        if($userID==0){
            return false;
        }
        $key = 'userid'.$userID;
        $redis = new RedisService();
//        $redis->set($key,[]);
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);

        $have = self::haveProductInRedis($redisArr,$id,$have);
        if($have){
            self::updateCartAttr($redisinfo,$id,$key,$redis);
            return true;
        }else{

            $res = BProduct::where('id',$id)
                ->find();

            $arr = [
                'id'=>$id,
                'name'=>$res->product_name,
                'img'=>$res->product_img,
                'counts'=>1,
                'price'=>$res->price,
                'show'=>$res->show,
                'checked'=>1,
                'cost'=>$res->cost
            ];
            array_push($redisArr,$arr);
            $value = json_encode($redisArr);
            $redis->set($key,$value);
            return true;
        }
    }


    public static function cartButton($id,$type){
        $have = 0;
        $userID = self::getUserId();
        if($userID==0){
            return false;
        }
        $key = 'userid'.$userID;
        $redis = new RedisService();
//        $redis->set($key,[]);
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);
//        self::updateCartAttr($redisinfo,$id,$key,$redis);
        $json = json_decode($redisinfo);
        $arr = [];
        foreach ($json as $k=>&$v){
            $item = (array)$v;
            if($item['id'] == $id){
                if($type == 2){

                    $item['counts'] += 1;
                }else{
                    $item['counts'] -= 1;
                }

            }
            array_push($arr,$item);

        }
        $value = json_encode($arr);
        $redis->set($key,$value);
        return [
            'code'=>200
        ];
    }

    /**
     * @param $arr
     * @return array
     * 将数据转为数组
     */
    public static function changeArr($arr){
        $arr1 = [];
        if($arr){
            $json = json_decode($arr);
            foreach ($json as $k=>&$v){
                $item = (array)$v;
                array_push($arr1,$item);
            }
        }

        return $arr1;
    }

    /**
     * @param $arr
     * @param $id
     * @param $have
     * @return int
     * 数组是否有当前的数据
     */
    protected static function haveProductInRedis($arr,$id,$have){
        if(count($arr)){
            foreach ($arr as $k=>&$v){
                $item = (array)$v;
                if($item['id'] == $id){
                    $have = 1;
                }
            }
        }
        return $have;
    }

    /**
     * @param $redisinfo
     * @param $id
     * @param $key
     * @param $redis
     * 购物车有该数据，然后修改该数据的数量
     */
    protected static function updateCartAttr($redisinfo,$id,$key,$redis){
        $json = json_decode($redisinfo);
        $arr = [];
        foreach ($json as $k=>&$v){
            $item = (array)$v;
            if($item['id'] == $id){
                $item['counts'] += 1;

            }
            array_push($arr,$item);

        }
        $value = json_encode($arr);
        $redis->set($key,$value);
    }

    /**
     * @param $token
     * @return int
     * 获取用户ID
     */
    public static function getUserId(){
        $res = 0;
        $header = request()->header();
        $token = $header['token'];
        $res1 = Cache::get($token);
        if($res1){
            $res = $res1['id'];
        }
        return $res;

    }

    public static function getCartCounts(){
        $userID = self::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        }
        $key = 'userid'.$userID;
        $redis = new RedisService();
//        $redis->set($key,[]);
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);
        $count = count($redisArr);
        return [
            'code'=>200,
            'msg'=>'获取数据成功',
            'data'=>$count
        ];
    }


    public static function showCart(){
        $userID = self::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        }
        $key = 'userid'.$userID;
        $redis = new RedisService();
//        $redis->set($key,[]);
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);
        return [
            'code'=>200,
            'msg'=>'获取数据成功',
            'data'=>$redisArr

        ];

    }

    public static function saveCache($phone,$id){
        $salt = 'hjhdhdhdj';
        $time = time();
        $key = md5($salt.$phone.$time);

        $value = [
            'id'=>$id,
            'phone'=>$phone
        ];

        $res = Cache::set($key,$value,0);
        if($res){
            return $key;
        }else{
            return '';
        }

    }

    public static function updateAttrInCart($type,$id){
        $arr = [];
        $userID = self::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        }
        $key = 'userid'.$userID;
        $redis = new RedisService();
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);
        foreach ($redisArr as &$v){
            if($type == 0){
                $v['checked'] = 0;
            }else if($type == 1){
                $v['checked'] = 1;
            }else if($type == 3){
                if($id == $v['id']){
                    $v['checked'] =1;
                }
            }else if($type == 4){

                if($id == $v['id']){
                    $v['checked'] =0;
                }
            }

        }
        unset($v);
        $value = json_encode($redisArr);
        $redis->set($key,$value);
        return [
            'code'=>200,
            'msg'=>'success'
        ];
    }

    /**
     * @return array
     * 获取购物车数据（数组格式）
     */
    public static function getCartToArray(){
        $userID = self::getUserId();
        $key = 'userid'.$userID;
        $redis = new RedisService();
//        $redis->set($key,[]);
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);
        return $redisArr;
    }

    public static function pay(){
        $arr = [];
        $money = 0;
        $cost =0;
        $userID = self::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        }
        $key = 'userid'.$userID;
        $redis = new RedisService();
//        $redis->set($key,[]);
        $redisinfo = $redis->get($key);
        $redisArr = self::changeArr($redisinfo);

        foreach ($redisArr as $v){
            if($v['checked']){
                array_push($arr,$v);
                $money +=$v['price']*$v['counts'];
                $cost +=$v['cost']*$v['counts'];
            }
        }

        if(!count($arr)){
            return [
                'code'=>404,
                'msg'=>'下单失败'
            ];
        }

        $addressKey = 'address'.$userID;
        $addressInfo = Cache::get($addressKey);

        $addressArr = self::changeArr($addressInfo);
        $address = $addressArr[0];

       $res = BOrder::create([
            'order_sn'=>BOrder::makeOrderNo(),
            'items'=>json_encode($arr),
            'create_time'=>time(),
            'status'=>2,
            'money'=>$money,
            'cost'=>$cost,
            'user_id'=>$userID,
           'address'=>json_encode($address)
        ]);
        if(!empty($res)){
            $newCartInfo = BOrder::delCartInfo($arr);
            $redis->set($key,json_encode($newCartInfo));
            return [
                'code'=>200,
                'msg'=>'下单成功',
                'data'=>$res->order_id
            ];
        }else{
            return [
                'code'=>404,
                'msg'=>'下单失败'
            ];
        }
    }

    public static function cartChecked(){
        $arr1 = [];
        $arr = self::getCartToArray();
        foreach ($arr as $v){
            if($v['checked']){
                array_push($arr1,$v);
            }
        }
        return $arr1;
    }


    public static function getCartByUserId(){
       $res = self::getUserId();

    }


}
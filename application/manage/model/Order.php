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
use app\manage\model\DeliveryOrder;
use app\manage\model\DeliveryUser;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;

class Order extends Model
{
    protected $hidden = ['delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return [];
        }
        return json_decode($value);
    }

    public function getSnapDeliveryAttr($value){
        if(empty($value)){
            return [];
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return [];
        }
        return json_decode(($value));
    }
    public static function hasNewOrders(){
        $redis = new RedisService();
        $res = $redis->get(config('redis.has_new_orders'));
        return $res;
    }

    public static function getNewOrders(){
        $info = [];
//        $redis = new RedisService();
        $res = self::hasNewOrders();
        if($res){
            $info = self::where('status','=',2)
                ->where('delivery_status','=',0)
                ->select()->toArray();
//            $redis->set(config('redis.new_orders'),$info);
        }
        return $info;


    }

    public static function myOrders(){
        $name = input('user');
        $user = DeliveryUser::where('delivery_name','=',$name)->find();
        $userID = $user->id;
        return self::where('delivery_status',$userID)->where('status',2)->select()->toArray();
    }


    public static function getOrder(){
        $orderID = input('order_id');
        $name = input('user');
        $user = DeliveryUser::where('delivery_name','=',$name)->find();
        $userID = $user->id;
        $have = self::orderHaveNone($orderID);
        if($have){
            return [
                'code'=>202,
                'msg'=>'订单已经被接单了'
            ];
        }else{
            $count = self::where('delivery_status',$userID)->count();
            if($count == 1){
                $res = new RedisService();
                $res->set(config('redis.has_new_orders'),0);
            }
            self::update([
                'id'=>$orderID,
                'delivery_status'=>$userID
            ]);

        }

    }
    //判断订单是否已经被接单
    public static function orderHaveNone($orderID){
        $res = self::where('id','=',$orderID)->find();
        if(!empty($res)){
            return false;
        }else{
            return true;
        }
    }
}
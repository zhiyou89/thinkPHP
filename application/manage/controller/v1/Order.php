<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 21:48
 */

namespace app\manage\controller\v1;

use think\Controller;

use app\manage\model\Order as OrderModel;
use app\manage\model\DeliveryUser as DeliveryUserModel;
use my\Redis as RedisService;

class Order extends Controller
{
    public function Index(){
        $info = [];
        $status = OrderModel::hasNewOrders();
       if($status){
           $info = OrderModel::getNewOrders();
       }
       return $info;

    }
    public function setRedis(){
        $res = new RedisService();
        $res->set(config('redis.has_new_orders'),1);
        return $res->get(config('redis.has_new_orders'));
    }

    public function initOrders(){
        return OrderModel::where('status',2)->where('delivery_status',0)->order('create_time desc') ->paginate(20, true, ['page' => input('pages')])->toArray();
    }

    public function myOrders(){
        $res = OrderModel::myOrders();
        return $res;
    }

    public function getDeliveryOrder(){
        $res = OrderModel::getOrder();
        return $res;
    }

    public function getUserOrder(){
        $id = DeliveryUserModel::getUserID(input('user_name'));
        return OrderModel::where('delivery_status',$id)->order('create_time','desc')->paginate(20, true, ['page' => input('pages')])->toArray();
    }

    public function getOrderStatus(){
       $res = OrderModel::where('id',input('order_id'))->find();
       return $res->status;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 23:28
 */

namespace app\api\model;


use app\api\server\Token;
use app\lib\enum\OrderStatusEnum;
use app\api\model\Product;
use app\api\model\Redis as RedisModel;

class Order extends BaseModel
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


    public static function getSummaryByUser($status, $page=1, $size=10)
    {
        $where = [];
        $uid = Token::getCurrentUid();
        $where['user_id'] = $uid;
        $where['status'] = $status;
        $where['show'] = 1;
        if($status == OrderStatusEnum::ALLORDER){
            unset($where['status']);
        }
        $pagingData = self::where($where)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        $pagingData->toArray();
        return $pagingData ;
    }

    public static function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

    public function products()
    {
        return $this->belongsToMany('Product', 'order_product', 'product_id', 'order_id');
    }


    //修改商品状态
    public static function updateReviewStatus($orderId,$productId,$status){
        $order = self::where('id','=',$orderId)
            ->find();
        $snapItems = $order->snap_items;
        $snapItems = json_decode( json_encode($snapItems),true);
        foreach ($snapItems as &$v){
            if($v['id']==$productId){
                $v['status'] = $status;
            }
        }
        unset($v);
        $uSnapItems = json_encode($snapItems);
        $res = self::where('id','=',$orderId)
            ->update([
                'snap_items'=>$uSnapItems
            ]);

        if($res){
            if($order->status == 4 && $status !=5){
                return $res;
            }
            if($status == 2){
                $res = self::where('id','=',$orderId)
                    ->update([
                        'status'=>4
                    ]);
            }else{
                foreach($snapItems as $v){
                    if($v['status'] !=$status){
                        $index = 1;
                    }
                };
                if(!isset($index)){
                    unset($index);
                    self::IfUpdateOrderStatus($orderId,$status);
                }
            }
        }
        return $res;
    }

    //是否修改订单状态1待支付2已支付3已完成4申请退款5取消6退款完成  商品状态2是申请退款3确认收货4评价
    public static function IfUpdateOrderStatus($orderId,$status){
        if($status==3 ||$status==5 ){
            self::where('id','=',$orderId)
                ->update([
                    'status'=>3
                ]);
        };


    }

    //付款成功改变所有商品的status
    public static function updateAllProductsByOrder($order){
        $snapItems = $order->snap_items;
        $snapItems = json_decode( json_encode($snapItems),true);
        foreach ($snapItems as &$v){
            //将订单中商品的status改为1
            $v['status'] = 1;
            Product::where('id',$v['id'])->setInc('sales',$v['counts']);
            RedisModel::updateSellNum($v['id'],$v['counts'],$v['index_category_id']);
        }
        unset($v);
        $snapItems = json_encode($snapItems);
        $res = self::where('id','=',$order->id)
            ->update([
                'snap_items'=>$snapItems
            ]);
    }


}
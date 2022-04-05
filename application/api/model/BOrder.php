<?php

namespace app\api\model;
use think\Cache;
use my\Redis as RedisService;
use app\api\model\BProduct;
use app\api\model\BUsers;
class BOrder extends BaseModel
{

    protected $hidden = ['user_id'];
    public function getItemsAttr($value)
    {
        if(empty($value)){
            return [];
        }
        return json_decode($value);
    }

    public function getAddressAttr($value)
    {
        if(empty($value)){
            return [];
        }
        return json_decode($value);
    }

    public function getCreateTimeAttr($value)
    {
        if(empty($value)){
            return [];
        }
        return  date("Y-m-d h:i:s", $value);
    }

    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    public static function delCartInfo(){
        $arrs = [];
        $array = BUsers::getCartToArray();
        foreach ($array as $v){
                if(!$v['checked']){
                    array_push($arrs,$v);
                }
            }
        return $arrs;
    }

    public static function getOrderInfo(){
        $userID = BUsers::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        }
        $id = input('id');
       return self::where('status',$id)
           ->where('user_id',$userID)
           ->order('create_time desc')
            ->select();
    }

    public static function getOneOrder(){
        $userID = BUsers::getUserId();
        if($userID==0){
            return [
                'code'=>400,
                'msg'=>'登录过期'
            ];
        }
        $id = input('id');
        $res = self::where('order_id',$id)
            ->find();
        return [
            'code'=>200,
            'msg'=>'success',
            'data'=>$res
        ];
    }

    public static function cancelOrder(){
        $id = input('id');
        $res = self::update([
            'order_id'=>$id,
            'status'=>4
        ]);
        if(!empty($res)){
            return [
                'code'=>200,
                'msg'=>'取消订单成功'
            ];
        }else{
            return [
                'code'=>400,
                'msg'=>'取消订单失败'
            ];
        }
    }
}
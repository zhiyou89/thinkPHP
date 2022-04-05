<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:44
 */

namespace app\api\model;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Log;
class Crontab extends BaseModel
{
    public function updateOrderStatusEveryDay(){
        $orderModel = new OrderModel();
        $arr = OrderModel::where('status',1)->field('id')->select()->toArray();
        if(is_array($arr)){
            foreach ($arr as &$v){
                $v['status'] = 5;
            }
        }
        $orderModel->saveAll($arr);
    }


    //
    public function updateFinishOrderStatus(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $where['create_time'] = array('between', array($beginToday,$endToday));
        $where['status'] = 2;
        Db::startTrans();
        try{
            $res = OrderModel::where($where)->field('id')->select()->toArray();
            foreach ($res as $order){
                $order = OrderModel::where('id',$order['id'])->find();
                $snapItems = $order->snap_items;
                $snapItems = json_decode( json_encode($snapItems),true);
                foreach ($snapItems as &$v){
                    if($v['index_category_id'] != 3){
                        $v['status'] = 3;
                    }else{
                        $index = 1;
                    }
                }
                unset($v);

                $uSnapItems = json_encode($snapItems);

                if(!isset($index)){
                    unset($index);
                    $res = OrderModel::where('id','=',$order['id'])
                        ->update([
                            'snap_items'=>$uSnapItems,
                            'status'=>3
                        ]);
                }else{
                    unset($index);
                    $res = OrderModel::where('id','=',$order['id'])
                        ->update([
                            'snap_items'=>$uSnapItems
                        ]);
                }

            }
            Db::commit();
        }catch (Exception $ex) {
            Db::rollback();
            Log::error($ex);
            // 如果出现异常，向微信返回false，请求重新发送通知
            return false;
        }


    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */
namespace app\api\server;

use app\api\model\Order;
use app\api\model\Product;
use app\api\model\UserCoupon;
use app\api\model\User;
use app\api\server\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;
use app\api\server\TxCms;
use my\Redis as RedisService;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $orderNo = $data['out_trade_no'];

            Db::startTrans();
            try {
                $order = Order::where('order_no', '=', $orderNo)->lock(true)->find();
                $order::update([
                    'id'=>$order->id,
                    'transaction_id'=>$data['transaction_id']
                ]);
                if ($order->status == 1) {
                    $service = new OrderService();
                    $status = $service->checkOrderStock($order->id);
                    if ($status['pass']) {
                        $this->updateOrderStatus($order->id, true);
                        $this->addCredits($order);
                        $this->reduceStock($status);
                        Order::updateAllProductsByOrder($order);
                        $this->useCoupon($order);
                        $this->setRedis();
                        $smsservice = new TxCms();
                        $smsservice->sendCms(+8618666261519);
//                        OrderService::fenYong($order);
                    } else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
            } catch (Exception $ex) {
                Db::rollback();
                Log::error($ex);
                // 如果出现异常，向微信返回false，请求重新发送通知
                return false;
            }
        }
        return true;
    }

   //优惠券
    private function useCoupon($order){
        if($order->coupon){
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            $where['create_time'] = array('between', array($beginToday,$endToday));
            $UserCouponInfo = UserCoupon::whereTime('create_time','Today')->where('coupon_id',$order->coupon)->field('id')->find();
            UserCoupon::update([
                'id'=>$UserCouponInfo->id,
                'show'=>0
            ]);
        }

    }
    private function setRedis(){
        $redis = new RedisService();
        $redis->set(config('redis.has_new_orders'),1);
    }
    private function reduceStock($status)
    {
//        $pIDs = array_keys($status['pStatus']);
        foreach ($status['pStatusArray'] as $singlePStatus) {
            Product::where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }

    private function updateOrderStatus($orderID, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        Order::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }

    private function addCredits($order)
    {
        User::where('id', '=', $order->user_id)
            ->setInc('credit' ,$order->total_price);
    }
}
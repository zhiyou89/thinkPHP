<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */

namespace app\api\server;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\model\Coupon as CouponModel;
use app\api\model\Setting as SettingModel;


Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class Refund
{

    //构建微信退款信息
    public function wxRefund($order_id,$refund_money){
        //查询订单,根据订单里边的数据进行退款
        $order = OrderModel::where('status',4)->where('id',$order_id)->find();
        if(empty($order)){
            return false;
        }
        $order = $order->toArray();
        $WxConfig = new \WxPayConfig();
        $merchid = $WxConfig::MCHID;
        $refundSn = $this->makeRefundNo();
        $input = new \WxPayRefund();

        $input->SetOut_trade_no($order['order_no']);        //自己的订单号
        $input->SetTransaction_id($order['transaction_id']);     //微信官方生成的订单流水号，在支付成功中有返回
        $input->SetOut_refund_no($refundSn);         //退款单号
        $input->SetTotal_fee($order['pay_price']*100);         //订单标价金额，单位为分
        $input->SetRefund_fee($refund_money*100);            //退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOp_user_id($merchid);

        $result = \WxPayApi::refund($input); //退款操作

        $res = [];
        $res['result'] = $result;
        $res['refund_sn'] = $refundSn;
        $res['user_id'] = $order['user_id'];
        return $res;
    }


    public static function makeRefundNo()
    {

        $Sn =
                'T' . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $Sn;
    }
}
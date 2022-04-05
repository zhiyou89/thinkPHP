<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */

namespace app\api\server;
use app\api\model\Order as OrderModel;
use app\api\model\Coupon as CouponModel;
use app\api\model\Setting as SettingModel;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\lib\exception\DeliveryPrice as DeliveryPriceException;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class Pay
{
    private $orderNo;
    private $orderID;
//    private $orderModel;

    function __construct($orderID)
    {
        if (!$orderID)
        {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }
    public function pay(){
        $this->checkOrderValid();
        $order = new Order();
        $status = $order->checkOrderStock($this->orderID);
        if (!$status['pass'])
        {
            return $status;
        }

       $orderInfo = OrderModel::where('id','=',$this->orderID)->find();

       $totalPrice = $this->getTotalPrice($orderInfo->total_price,$orderInfo->coupon);

        OrderModel::update([
           'id'=> $this->orderID,
            'pay_price'=>$totalPrice
        ]);
        return $this->makeWxPreOrder($totalPrice );

    }


    /**
     * @return bool
     * @throws OrderException
     * @throws TokenException
     */
    private function checkOrderValid()
    {
        $order = OrderModel::where('id', '=', $this->orderID)
            ->find();

        if (!$order)
        {
            throw new OrderException();
        }

//        $currentUid = Token::getCurrentUid();
        if(!Token::isValidOperate($order->user_id))
        {
            $TokenException = new TokenException();
            $TokenException->msg = "订单与用户不匹配";
            $TokenException->errorCode = 10003;
            throw $TokenException;
        }
        if($order->status != 1){
            $OrderException = new OrderException();
            $OrderException->msg = "订单已支付过啦";
            $OrderException->errorCode = 80003;
            $OrderException->code = 400;
            throw $OrderException;
        }
        $this->orderNo = $order->order_no;
        return true;
    }

    private function getTotalPrice($totalPrice,$couponId){
        $coupon = 0;
        $setting = SettingModel::get(1);
//        $order = OrderModel::where('id', '=', $this->orderID)
//            ->find();

        $delivery = $setting->delivery_free;

        if($couponId){
            $CouponModel = CouponModel::get($couponId);
            $coupon = $CouponModel->money;
        }
        $totalPrice = $totalPrice + $delivery - $coupon;
        return $totalPrice;
    }

    // 构建微信支付订单信息
    private function makeWxPreOrder($totalPrice)
    {
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid)
        {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();

        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('篮蜂商城');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }


    //向微信请求订单号并生成签名
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);

        // 失败时不会返回result_code
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
            return json($wxOrder,400);
        }

        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }
    private function recordPreOrder($wxOrder){
        // 必须是update，每次用户取消支付后再次对同一订单支付，prepay_id是不同的
        OrderModel::where('id', '=', $this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    // 签名
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }
}
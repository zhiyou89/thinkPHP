<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:28
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\server\Refund as RefundService;
use app\api\model\Order as OrderModel;
use app\api\model\Refund as RefundModel;
use app\api\model\User;
class Refund extends BaseController
{

    public function wxRefund(){
        //订单ID
        $id = input('id');
        //退款的商品ID
        $productID = input('product_id');
        $refundMoney = input('money');
        $pay = new RefundService();
        $result = $pay->wxRefund($id,$refundMoney);
        if(($result['result']['return_code']=='SUCCESS') && ($result['result']['result_code']=='SUCCESS')){
            OrderModel::updateReviewStatus($id,$productID,5);
            User::where('id',$result['user_id'])->setDec('credit',floor($refundMoney));
//            OrderModel::update([
//                'id'=>$id,
//                'status'=>6
//            ]);
            RefundModel::create([
                'order_id'=>$id,
                'refund_money'=>$refundMoney,
                'refund_sn'=>$result['refund_sn'],
                'create_time'=>date('Y-m-d H:i:s',time())
            ]);
            $res = [
                'errorCode'=>200,
                'msg'=>'退款成功'
            ];
            //退款成功
        }else{
            $res = [
                'errorCode'=>400,
                'msg'=>'退款失败'
            ];
        }
        return $res;
    }
}
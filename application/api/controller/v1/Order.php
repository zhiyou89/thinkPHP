<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 21:48
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\Order as OrderValidate;
use app\api\validate\OrderPlace;
use app\lib\exception\OrderException;
use app\api\model\Product as ProductModel;
use app\api\server\Order as OrderService;
use app\api\server\Token as TokenService;
use app\api\server\Token;
use think\Request;

class Order extends BaseController
{

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    /**
     * 根据订单状态获取订单数据
     * $status为10表示全部订单
     * @param $status
     * @param $page
     * @return \think\Paginator
     * @throws OrderException
     * @throws \think\Exception
     */
    public function showOrderByStatus($status,$page){
        (new OrderValidate())->goCheck();
        $res = OrderModel::getSummaryByUser($status,$page);
//        if($res->isEmpty()){
//            return [];
////            throw new OrderException();
//        }
        return $res;
    }

    /**
     * 下单
     * @url /order
     * @HTTP POST
     */
    public function placeOrder()
    {
//       (new OrderPlace())->goCheck();
        $data = input();
        $products = $data['products'];
        $delivery = $data['delivery'];
        $address = $data['address'];
        $coupon = $data['coupon'];
        $uid = TokenService::getCurrentUid();
        $order = new OrderService();

        $status = $order->place($uid, $products, $delivery, $address,$coupon);
        return json([
            'code'=>200,
            'msg'=>'success',
            'data'=>$status
        ]);
    }

    public function getOrderInfoByID($id){
        (new IDMustBePositiveInt())->goCheck();
        return OrderModel::get($id);
    }

    //修改商品状态
    public  function updateProductStatus(){
        $request = Request::instance();
        $params = $request->param();
        $orderId = $params['order_id'];
        $productId = $params['product_id'];
        $status = $params['status'];
        $res = OrderModel::updateReviewStatus($orderId,$productId,$status);
        if($res){
           return json([
                    'statusCode'=>200,
                    'msg' =>'操作成功'
                ],200);
        }else{
            return json([
                'statusCode'=>500,
                'msg' =>'操作失败'
            ],500);
        }

    }

    //取消订单
    public function cancelOrder($id){
        (new IDMustBePositiveInt())->goCheck();
        $res = OrderModel::where('id','=',$id)
            ->update([
                'status'=>5
            ]);
        if($res){
            return json([
                'status'=>200,
                'msg'=>"取消订单成功"
            ],200);
        }
    }

    //获取代付款和退款中的订单数量
    public function getOrderStatusSum(){
        $res = [];
        $where = [];
         $uid = TokenService::getCurrentUid();
        //待付款数量
       $daifu = OrderModel::where('user_id',$uid)->where('status',1)->where('show',1)->count();
       $tuikuan = OrderModel::where('user_id',$uid)->where('status',4)->where('show',1)->count();
       return [
           'daifu'=>$daifu,
           'tuikuan'=>$tuikuan
       ];
    }

    //软删除订单
    public function delOrder(){
        OrderModel::update([
            'id'=>input('order_id'),
            'show'=>0
        ]);
        return true;
    }

    public function fenYong(){
        OrderService::fenYong();
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:35
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Reviews as reviewsModel;
use app\api\server\Token as tokenService;
use app\api\model\ProductReview as productReviewModel;
use app\api\model\Order as orderModel;
use think\Request;

class Review extends BaseController
{
    public function getReviewsByProduct(){
        orderModel::updateReviewStatus(667,1);
    }

    public function saveReview(){
        $request = Request::instance();
        $params = $request->param();
        $reviews = reviewsModel::create([
            'user_id' => tokenService::getCurrentUid(),
            'review_content' => $params['text'],
            'review_imgs' => serialize($params['imgs']),
            'score' => $params['productReview'],
            'create_time'=>time(),
            'service_score'=>$params['serviceReview'],
            'delivery_score'=>$params['deliveryReview']
        ]);

        if($reviews->id){
            productReviewModel::saveInfo($params['productId'],$reviews->id,$params['productReview']);
            orderModel::updateReviewStatus($params['orderId'],$params['productId'],4);
            return json([
                'statusCode'=>200,
                'msg' =>'操作成功'
            ],200);
         }else{
            return json([
                'statusCode'=>500,
                'msg' =>'系统错误'
            ],500);
        }
    }

}
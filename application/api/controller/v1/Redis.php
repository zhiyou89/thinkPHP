<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 2:51
 */

namespace app\api\controller\v1;
use think\Controller;
use app\api\model\Redis as RedisModel;
class Redis extends Controller
{

    public function saveNowDeliveryProducts(){
        return RedisModel::getNowDeliveryProducts(false);
    }
    public function saveTomorrowDeliverProducts(){
        return RedisModel::getTomorrowDeliverProducts(false);
    }
    public function saveRealityDeliverProducts(){
        return RedisModel::getRealityDeliverProducts(false);
    }

}
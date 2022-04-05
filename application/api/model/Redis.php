<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 19:54
 */

namespace app\api\model;

use app\api\model\Product as ProductModel;
use my\Redis as RedisService;


class Redis extends BaseModel
{
//    protected static $redis = null;
//    public function __construct()
//    {
//        self::$redis = new RedisService();
//    }

    /**
     * @return mixed
     * 获取即时配送的商品信息
     */
    public static function getNowDeliveryProducts($boolen = true){
        $redis = new RedisService();
        $NowDeliveryProducts = $redis->get(config('redis.now_products'));
        if(!$boolen){
            $NowDeliveryProductsBySql = ProductModel::getNowDeliverProducts();
            $redis->set(config('redis.now_products'),$NowDeliveryProductsBySql);
            $NowDeliveryProducts = $redis->get(config('redis.now_products'));
        }
        return $NowDeliveryProducts;
    }


    /**
     * @return mixed
     * 获取明天配送商品
     */
    public static function getTomorrowDeliverProducts($boolen = true){

        $redis = new RedisService();
        $TomorrowDeliveryProducts = $redis->get(config('redis.tomorrow_products'));
        if(!$boolen){
            $TomorrowDeliveryProductsBySql = ProductModel::getTomorrowDeliverProducts();
            $redis->set(config('redis.tomorrow_products'),$TomorrowDeliveryProductsBySql);
            $TomorrowDeliveryProducts = $redis->get(config('redis.tomorrow_products'));
        }
        return $TomorrowDeliveryProducts;
    }

    /**
     * @return mixed
     * 获取产地直发商品
     */
    public static function getRealityDeliverProducts($boolen = true){
        $redis = new RedisService();
        $RealilyDeliveryProducts = $redis->get(config('redis.realily_products'));
        if(!$boolen){
            $RealilyDeliveryProductsBySql = ProductModel::getRealityDeliverProducts();
            $redis->set(config('redis.realily_products'),$RealilyDeliveryProductsBySql);
            $RealilyDeliveryProducts = $redis->get(config('redis.realily_products'));
        }
        return $RealilyDeliveryProducts;
    }

    public static function getAllProducts(){
        $redis = new RedisService();
        $allData = $redis->get(config('redis.AllProducts'));
        if(!$allData){
            $all = ProductModel::getAllProductsForIndexPage();
            $redis ->set(config('redis.AllProducts'),$all);
            $allData = $redis->get(config('redis.AllProducts'));
        }
        return $allData;
    }

    //修改销量
    public static function updateSellNum($productID,$num,$indexCategoryId){
        $now = self::getNowDeliveryProducts();
        $tomorrow = self::getTomorrowDeliverProducts();
        $reality = self::getRealityDeliverProducts();
        $CategoryId = 2+$indexCategoryId;
        foreach ($now as &$n){
            if($n['id']==$productID  && $n['index_category_id'] ==$indexCategoryId){
                $n['sales'] += $num;
            }
        }

        foreach ($tomorrow as &$t){
            if($t['id']==$productID  && $t['index_category_id_2'] ==$CategoryId){
                $t['sales'] += $num;
            }
        }
        foreach ($reality as &$r){
            if($r['id']==$productID  && $r['index_category_id_3'] ==$CategoryId){
                $r['sales'] += $num;
            }
        }
        unset($n);
        unset($t);
        unset($r);
        $redis = new RedisService();
        $redis->set(config('redis.now_products'),$now);
        $redis->set(config('redis.tomorrow_products'),$tomorrow);
        $redis->set(config('redis.realily_products'),$reality);
    }

}
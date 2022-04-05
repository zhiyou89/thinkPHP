<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 19:24
 */

namespace app\api\model;

use my\Redis as RedisService;


class Product extends BaseModel
{
    private $i;
    protected $autoWriteTimestamp = 'datetime';
    protected $hidden = [
        'delete_time', 'main_img_id', 'pivot', 'from',"img_id",'summary',
        'create_time', 'update_time','hot_rank','stock'];

    protected function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }
//    protected function getPriceAttr($value,$data){
//        $price = $value;
//
//        if($data['index_category_id_2']==4){
//            $price = $data['price_2'];
//        }
//        if($data['index_category_id_3']==5){
//            $price = $data['price_3'];
//        }
////        if($data['index_category_id']==1){
////            $price = $value;
////        }elseif ($data['index_category_id_2']==4){
////            $price = $data['price_2'];
////        }elseif ($data['index_category_id_3']==5){
////            $price = $data['price_3'];
////        }
//        return $price;
//    }
//    protected function getIndexCategoryIdAttr($value,$data){
//        $string = '';
//        if($data['index_category_id']==1){
//            $string = '即时配送';
//        }elseif ($data['index_category_id_2']==4){
//            $string = '隔天配送';
//        }elseif ($data['index_category_id_3']==5){
//            $string = '产地直发';
//
//        }
//        return $string;
//
//    }
    public function productImgs(){
        return  $this->hasMany('ProductImage','product_id','id');
    }

    public function banners(){
        return $this->hasMany('ProductImage', 'banner_product_id', 'id');
    }
    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }



    public function tags(){
        return $this->belongsToMany('tag', 'product_tag', 'tag_id', 'product_id');
    }

    public function menu(){
        return $this->hasMany('product_menu','product_id','id');
    }

    public function reviews(){
        return $this->hasMany('product_review','product_id','id');
    }

    public static function getProductsByCategoryID($categoryID){
        $res = self::with(['tags'])->where('category_id', $categoryID)->where('onsell',1)->select();
        return $res;
    }
    public static function getIndexPageHotProduct(){
        return self::where('hot_rank','=',77)
            ->where('onsell','=',1)
//            ->order('hot_rank','desc')
//            ->limit(2)
            ->select();
    }

    /**
     * @param $categoryID
     * @param $pages
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * 商品列表页数据查询
     */
    public static function getProductionInCategory($id){
        $redis = new RedisService();
        if($id==1){
            $redisName = config('redis.now_products');
        }elseif ($id ==2){
            $redisName = config('redis.tomorrow_products');
        }else{
            $redisName = config('redis.realily_products');
        }
        $products = $redis->get($redisName);
        $result = getRandomByArray($products,8);
        return $result;

    }


    public static function getProductDetail($id){
        $data =  self::with([
            'productImgs' => function($query){
                $query->with('imgUrl')->order('order','asc');
                }
            ])
            ->with([
                'banners' => function($query){
                    $query->with('imgUrl');
                }
            ])
            ->with(['properties','tags'])

            ->find($id);
        $data['delivery_time'] = date('H',time()).":00-".date('H',time()+7200).":00";
        return $data;
    }

    public function getProduct($id,$pages){
        $redis = new RedisService();
        $Current = ($pages-1)*10;
        if($id == 1){
            $NowDeliveryProducts = $redis->get(config('redis.now_products'));
            if(!$NowDeliveryProducts){
                $NowDeliveryProductsBySql = self::getNowDeliverProducts();
                $redis->set(config('redis.now_products'),$NowDeliveryProductsBySql);
                $NowDeliveryProducts = $redis->get(config('redis.now_products'));
            }
            $res = $NowDeliveryProducts;
        }elseif ($id == 2){
            $TomorrowDeliveryProducts = $redis->get(config('redis.tomorrow_products'));
            if(!$TomorrowDeliveryProducts){
                $TomorrowDeliveryProductsBySql = self::getTomorrowDeliverProducts();
                $redis->set(config('redis.tomorrow_products'),$TomorrowDeliveryProductsBySql);
                $TomorrowDeliveryProducts = $redis->get(config('redis.tomorrow_products'));
            }
            $res = $TomorrowDeliveryProducts;
        }else{
            $RealilyDeliveryProducts = $redis->get(config('redis.realily_products'));
            if(!$RealilyDeliveryProducts){
                $RealilyDeliveryProductsBySql = self::getRealityDeliverProducts();
                $redis->set(config('redis.realily_products'),$RealilyDeliveryProductsBySql);
                $RealilyDeliveryProducts = $redis->get(config('redis.realily_products'));
            }
            $res = $RealilyDeliveryProducts;
        }

        $result = array_slice($res,$Current,10);
        return $result;
    }


    //redis获取所有首页商品数据
    public static function getAllProductsForIndexPage(){
        return  self::with('properties')->where('onsell',1)->select()->toArray();
    }

    //所有隔天配送商品
    public static function getTomorrowDeliverProducts(){
        $res = self::with('properties')->where('onsell',1)->where('index_category_id_2',4)->select()->toArray();
        return shuffle_assoc($res);
    }

    //即时配送商品
    public static function getNowDeliverProducts(){
        $res = self::with('properties')->where('onsell',1)->where('index_category_id',1)->select()->toArray();
        return shuffle_assoc($res);
    }
    //产地直发
    public static function getRealityDeliverProducts(){
        $res = self::with('properties')->where('onsell',1)->where('index_category_id_3',5)->select()->toArray();
        return shuffle_assoc($res);
    }
//
//    public static function randProduct($count){
//        foreach ()
//        print_r($count);
//    }



    public static function getMenuByProductID($id){
        return self::with([
            'menu'
            => function($query){
                $query->with(['menuInfo'])->limit(4);
            }
        ])->where('id','=',$id)
            ->select();
    }

    public static function getReviewsByProduct($id){
        $where = [];
        $where['id'] = $id;
        $data = self::with([
            'reviews'=>function($query){
                    $query->with([
                            'reviewsInfo' => function($query1){
                                $query1->with(['userInfo']);
                            }
                    ])
                        ->with([
                            'reviewsCategory' => function($query2){
                                $query2->with(['categoryName']);
                            }
                        ])->order('review_id','desc');
                        //->with(['reviewsCategory.categoryName'])->order('review_id','desc');
//                $query->with(['reviewsInfo.userInfo','reviewsCategory.categoryName']);
            }
        ])->where($where)
            ->find();
        return $data;
    }
    public static  function getReviews($id){
        $count = 0;
        $number = 0;
        $arr = [];
        $K = [];
        $data = self::getReviewsByProduct($id);
        $data = $data->toArray();
        if(is_array($data['reviews'])){
            foreach ($data['reviews'] as $k=>$y){
                if($y['score']<4){
                    array_unshift($K,$k);
                }
                $count += $y['score'];
                $number = $k+1;
                foreach ($y['reviews_category'] as $c=>$v){
                    array_unshift($arr,$v['category_name']['category_name']);
                }

            }
            $data['average_score'] =5;
            $data['tag'] = array_count_values($arr);
            $data['member'] = $number;
        }

       return $data;
    }

    public static function searchProduct($data,$pages){
        return self::where('product_describe','like','%'.$data.'%')
            ->where('onsell','=',1)
            ->order('hot_rank','desc')
            ->order('create_time','desc')
            ->paginate(20, true, ['page'=>$pages]);
    }

    public static function getSellMany($pages){
        return self::with('properties')->where('list',1)
            ->where('index_category_id_2',4)
            ->where('onsell','=',1)
            ->where('list',1)
            ->order('hot_rank','desc')
            ->order('create_time','desc')
            ->paginate(20, true, ['page'=>$pages]);
    }

    public static function getCartInfo(){
        print_r(input('ids'));
//        return self::whereIn('id',input('ids'))->select();
    }

    public function getPoductTruePrice($navID,$productData){
        if($navID==2){
            $productData['price'] = $productData['price_2'];
        }elseif ($navID==3){
            $productData['price'] = $productData['price_3'];
        }

        return $productData;
    }

}
<?php

namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\CategotyValidate;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\OrderException;
use app\lib\exception\ProductException;
use think\Controller;
use think\Db;


class Product extends Controller
{

    public function showIndexPageHotProduct(){
        $res = ProductModel::getIndexPageHotProduct();
        return $res;
    }
    /**
     * @param $categoryID 商品分类id
     * @param $page 当前页
     * @return \think\Paginator
     * @throws ProductException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $pagingProducts = ProductModel::getProductionInCategory($id);
        return $pagingProducts;

    }

    /**
     * @param $id 商品id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\Exception
     * 单个商品详情
     */
    public function getOne($id){

        (new IDMustBePositiveInt())->goCheck();
        $res =  ProductModel::getProductDetail($id);
        if(!$res){
            throw new OrderException();
        }
        return $res;
    }

    public function showMenuByProductID($id){
        (new IDMustBePositiveInt())->goCheck();
        $res =  ProductModel::getMenuByProductID($id);
        if($res->isEmpty()){
            return $res = [];
        }
        return $res;
    }

    public function  showReviews($id){
        (new IDMustBePositiveInt())->goCheck();
        $res = ProductModel::getReviews($id);
        return  $res;
    }
    public function searchProduct($pages){
        $where = input('data');
        $res = ProductModel::searchProduct($where,$pages);
        return $res;
    }

    public function getCartData(){
        $ids = input('ids');
        return Db::table('product')->whereIn('id',$ids)->field('id,price,price_2,price_3,onsell,index_category_id,index_category_id_2,index_category_id_3')->select();
    }

    public function getSellMany(){
        $pages = input('pages');
        return ProductModel::getSellMany($pages);
    }

    /**
     * 首页活动1商品信息8条
     */
    public function getListOneIndex(){
        $res = ProductModel::where('list',1)->where('index_category_id_2',4)->order('hot_rank desc')->limit(8)->select();
        foreach ($res as &$v){
             $v['index_category_id'] = "隔天配送";
             $v['price'] = $v['price_2'];
        }
        unset($v);
        return $res;
    }
    
}

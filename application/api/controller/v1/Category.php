<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 22:57
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Category as CategoryModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\CategoryException;
use app\api\model\Product as ProductModel;


class Category extends BaseController
{
    /**
     * @param $id 1为商品分类 2菜谱分类
     * @return false|\PDOStatement|string|\think\Collection
     * @throws CategoryException
     * @throws \think\Exception
     * 分类
     */
    public function getAllCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $categories = CategoryModel::all(['list_location'=>$id]);
//        $categories = CategoryModel::showGoodListOrMenuList($id);
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }

    public function getIndexCategory($id,$pages=1){
        (new IDMustBePositiveInt())->goCheck();
        $productModel =new ProductModel();
        $product = $productModel->getProduct($id,$pages);
        return $product;
    }

    public function getListPageProductByCategory(){

        $where = [];
//        $where['category_id'] = input('categoryId');
        $id = input('indexCategoryID');
//        $where['index_category_id'] = input('indexCategoryID');
//        $where['onsell'] = 1;
        $pages = input('pages');
        if($id==1){
            $where = [
                'index_category_id'=>1,
                'onsell'=>1,
                'list'=>0,
                'category_id'=>input('categoryId')
            ];
        }elseif ($id==2){
            $where = [
                'index_category_id_2'=>4,
                'onsell'=>1,
                'list'=>0,
                'category_id'=>input('categoryId')
            ];
        }else{
            $where = [
                'index_category_id_3'=>5,
                'onsell'=>1,
                'list'=>0,
                'category_id'=>input('categoryId')
            ];
        }
        $product = ProductModel::with('properties')->where($where)->paginate(10, true, ['page'=>$pages])->toArray();
        return $product;
    }
}
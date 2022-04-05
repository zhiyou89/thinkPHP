<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller\v1;



use app\admin\controller\Base;
use app\admin\model\Product;
use app\admin\model\Order as OrderModel;
use app\admin\model\Category;
use app\admin\model\ProductMenu as productMenuModel;
use think\Request;

class AproductController extends Base
{
    public function Index(){
        $obj = new Product();
        $obj->Index();
    }
    //获取所有商品
    public function AllProduct(){
        $res = Product::getAllProduct();

        return [
            'status'=>200,
            'msg'=>'success',
            'data'=>$res['data'],
            'count'=>$res['count'],
            'current'=>$res['current']
        ];
    }
    //上下架
    public function OnSell(){
        return Product::onSell();
    }
    //获取单个商品
    public function OneProduct(){
       return Product::getOneProduct();

    }

    //商品分类
    public function CategoryByProduct(){
        return Category::getProductCategory();
    }

    //写入或更改product
    public function saveProductInfo(){
        return Product::saveProductData();
    }

    public function  snapProduct(){
       return OrderModel::getSnapProduct();
    }

    //菜谱关联商品
    public function linkProductByMenu(){
        $request = Request::instance();
        $param = $request->param();
        $type = $param['type'];
        $menuID = $param['id'];
        $product = Product::where('product_describe','=',$param['product_id'])->find();
        if(empty($product)){
            return json([
                'statusCode'=>404,
                'msg'=>'商品不存在',
            ]);
        }else{
            $res = productMenuModel::linkProduct($product->id,$type,$menuID);
            if($res){
               return json([
                   'statusCode'=>200,
                   'msg'=>'操作成功',
               ]);
           }
        }

    }

}

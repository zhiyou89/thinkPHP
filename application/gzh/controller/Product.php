<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\controller;
use think\Controller;
use app\gzh\model\BProduct;

class Product extends Controller
{
    public function addCart($id){
       $product = db('b_product')
       ->where('id',$id)
       ->field('id,price,product_name,product_img')
       ->find();
       print_r($product);
    }
}
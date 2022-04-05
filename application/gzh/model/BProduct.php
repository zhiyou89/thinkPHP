<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\model;

use think\Model;

class BProduct extends Model
{
    public static function productOne($id){
       return self::where('id',$id)
//           ->field('id','product_name','product_img','show','price')
           ->find();
    }
}
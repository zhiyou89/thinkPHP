<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 23:30
 */

namespace app\api\model;


class ProductMenu extends BaseModel
{
//    protected $hidden = ['product_id','menu_id','id'];
    public function menuInfo(){
        return $this->belongsTo('menu',"menu_id",'id');
    }


    public function productOne(){
        return $this->belongsTo('Product',"product_id",'id');
    }


}
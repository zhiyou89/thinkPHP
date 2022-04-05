<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Request;

class Category extends BaseModel
{
    protected $hidden = ['description', 'delete_time', 'update_time','topic_img_id','list_location'];
    public static function getProductCategory(){
       return self::where('list_location','=',1)->select();
    }
}
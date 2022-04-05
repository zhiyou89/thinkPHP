<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 19:32
 */

namespace app\admin\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['img_id', 'delete_time', 'product_id'];
    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
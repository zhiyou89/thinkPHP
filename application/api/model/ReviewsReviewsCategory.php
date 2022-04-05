<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 19:54
 */

namespace app\api\model;


class ReviewsReviewsCategory extends BaseModel
{
    protected $hidden = ['review_id','category_id','id'];
    public function categoryName(){
        return $this->belongsTo('reviews_category',"category_id",'id');
    }
}
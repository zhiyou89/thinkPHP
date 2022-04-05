<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 19:54
 */

namespace app\api\model;


class ProductReview extends BaseModel
{
    protected $hidden = ['pivot','product_id','review_id','id'];
    public function reviewsInfo(){
        return $this->belongsTo('reviews',"review_id",'id');
    }
    public function reviewsCategory(){
        return  $this->hasMany('reviews_reviews_category','review_id','review_id');
    }

    public static function saveInfo($productId,$review_Id,$score){
        self::create([
            'product_id'=>$productId,
            'review_id'=>$review_Id,
            'score'=>$score
        ]);
    }
}
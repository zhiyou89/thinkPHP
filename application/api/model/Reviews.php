<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 19:54
 */

namespace app\api\model;


class Reviews extends BaseModel
{
    protected $hidden = ['pivot'];
    protected function getReviewImgsAttr($value){
        $arr = unserialize($value);
        foreach ($arr as &$v){
            $v = substr($v, 1);
            $v =  substr($v, 0, -1);
            $v = stripslashes($v);
            $v = 'https://youguan-1257044613.cos.ap-guangzhou.myqcloud.com/new/'.$v;
        }
        unset($v);
        return $arr;
    }
    public function category(){
        return $this->hasMany('reviews_reviews_category','review_id','id');
    }

    public function userInfo(){
        return $this->belongsTo('user','user_id','id');
    }

}
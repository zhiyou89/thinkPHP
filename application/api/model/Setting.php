<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 1:17
 */

namespace app\api\model;


class Setting extends BaseModel
{
    public function getBannerJumpAttr($value)
    {
        if(empty($value)){
            return [];
        }
        return 1;
    }

    public function getNoticeContentAttr($value){
        if(empty($value)){
            return [];
        }
        return json_decode($value);
//        return unserialize($value);
    }

    public static function showSettings(){
        return self::where('id',1)->find();
    }

}
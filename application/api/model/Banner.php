<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\api\model;


class Banner extends BaseModel
{
    protected  $table = 'banner';
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerByID($id){
        $banner = self::with([
            'items' => function($query){
                $query->with('imgUrl');
            }
        ])->find($id);
        return $banner;
    }
}
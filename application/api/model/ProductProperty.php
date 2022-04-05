<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/20
 * Time: 2:01
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden=['product_id', 'delete_time', 'id','update_time'];

    protected function getDetailAttr($value,$data){
        $arr = [];
        $arrs = [];
        $arr = explode(',',$value);
        foreach ($arr as $k=>$v){
            $arrs[$k]['name'] = $v;
            $arrs[$k]['index'] = 0;
            if($k==0){
                $arrs[$k]['index'] = 1;
            }
        }
        return $arrs;
    }
}
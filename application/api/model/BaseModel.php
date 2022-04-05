<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    //
    protected function prefixImgUrl($value, $data)
    {
        $imgUrl = $value;
        if($data['from'] == 2){
            $imgUrl = config('setting.img_prefix').$value;
        }
        return $imgUrl;
    }


    public function getCreateTimeAttr($value)
    {
        return date("Y-m-d ", $value);
    }

    public function imgUrl(){
        return $this->belongsTo('Image',"img_id",'id');
    }

    public function coupon(){
        return $this->belongsTo('coupon',"coupon_id",'id');
    }
}

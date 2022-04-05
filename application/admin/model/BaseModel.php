<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Model;

class BaseModel extends Model
{
    protected function prefixImgUrl($value, $data)
    {
        $imgUrl = $value;
        if($data['from'] == 2){
            $imgUrl = 'https://youguan-1257044613.cos.ap-guangzhou.myqcloud.com/new/'.$value;
        }
        return $imgUrl;
    }

    protected function prefixOnSell($value){
        $onLine = '在售';
        if(!$value){
            $onLine = '已下架';
        }
        return $onLine;
    }

    public function imgUrl(){
        return $this->belongsTo('Image',"img_id",'id');
    }
}
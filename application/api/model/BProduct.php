<?php

namespace app\api\model;

class BProduct extends BaseModel
{
    public function getProductImgAttr($value)
    {
        return 'https://youguan-1257044613.cos.ap-guangzhou.myqcloud.com/new/' . $value;
    }
    public function getProductDetailAttr($value)
    {
        return 'https://youguan-1257044613.cos.ap-guangzhou.myqcloud.com/new/' . $value;
    }
    public function getPerAttr($value)
    {
        $str = '';
        if($value == 1){
            $str = '/斤';
        }else if($value == 2){
            $str = '/个';
        }else if($value == 3){
            $str = '/件';

        }else if($value == 4){
            $str = '/板';
        }else if($value == 5){
            $str = '/包';
        }else if($value == 6){
            $str = '/瓶';
        }
        return $str;
    }
    public static function getProductData($id,$page){
        $where = [];
            $where = [
                'category'=>$id
            ];
        return self::where('show',1)
            ->where($where)
            ->order('sort desc')
            ->select();
    }

    public static function searchGoods($str){
        return self::where('product_name','like','%'.$str.'%')
            ->select();
    }
}


<?php


namespace app\admin\model;


use think\Request;

class ProductMenu extends BaseModel
{

    public static function linkProduct($productID,$type,$menuID){
        $where = [];
        $where['product_id'] = $productID;
        $where['menu_id'] = $menuID;
        $where['type'] = $type;
        $productMenu = self::where($where)->find();
        if(empty($productMenu)){
            $res = self::create([
                'product_id'=>$productID,
                'type'=>$type,
                'menu_id'=>$menuID
            ]);
        }else{
            $res = 1;
        }

        return $res;
    }
    
}
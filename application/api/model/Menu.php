<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 20:43
 */

namespace app\api\model;


class Menu extends BaseModel
{
    protected $hidden = ['delete_time','update_time','createtime','hot_rank','pivot'];

    public function getMenuItemsAttr($value)
    {
        $res= json_decode($value);
        return $res;
    }
    public function allMenuByCategory(){
        return $this->belongsToMany('tag','menu_tag','tag_id','menu_id');
    }

    public function imgs(){
        return   $this->hasMany('MenuImage','menu_id','id');
    }

    public function tags(){
        return $this->belongsToMany('tag', 'menu_tag', 'tag_id', 'menu_id');
    }

    public function menuProduct(){
        return $this->belongsToMany('menu', 'product', 'product_id', 'menu_id');
    }

    public function menuFindProduct(){
        return   $this->hasMany('productMenu','menu_id','id');
    }


    public static function getAllMenuInCategory($categoryID,$page){
        $where = [];
        if($categoryID != config("setting.menu_by_category_hot")){
            $where['category_id'] = $categoryID;
        }
        return self::with('allMenuByCategory')
            ->where($where)
            ->order('menu_hot','desc')
            ->paginate(3, true, ['page'=>$page]);
    }

    public static function getSingleMenu($id)
    {
        return self::with([
                'imgs'
                => function($query){
                    $query->with(['imgUrl'])->order('order','asc');
                }
            ])
            ->with('tags')
            ->with([
                'menuFindProduct','menuFindProduct.productOne'
//                =>function($query){
//                    $query->with(['productOne']);
//                }
            ])
            ->where('id','=',$id)
            ->select();
    }

    public static  function getMainProductByMenu($id){
        return self::with([
            'menuFindProduct'
            =>function($query){
                $query->with('productOne.properties');
            }
        ])
        ->where('id','=',$id)
        ->select();

    }

}
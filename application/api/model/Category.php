<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 22:57
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['topic_img_id', 'delete_time', 'update_time','list_location'];
    public function img()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public static function showGoodListOrMenuList($id){
        $where = [
            "list_location" => $id
        ];
        return self::with('img')->where($where)->select();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 22:19
 */

namespace app\api\model;


class MenuImage extends BaseModel
{
    protected $hidden = ['menu_id','img_id','order','delete_time'];

}
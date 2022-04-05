<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller\v1;



use app\admin\controller\Base;
use app\admin\model\Menu  as MenuModel;

class Menu extends Base
{
    public function AllMenuInfo(){
        $res = MenuModel::getAllMenu();
        return $res;
    }

    public function saveMenu(){
        $res = MenuModel::updateMenuInfo();
        return $res;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 20:37
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Menu as MenuModel;
use app\api\validate\CategotyValidate;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MenuException;

class Menu extends BaseController
{
    /**
     * @param $categoryID
     * @param $page
     * @return \think\Paginator
     * @throws MenuException
     * @throws \think\Exception
     * 菜谱列表页
     */
    public function getAllMenuByCategory($categoryID,$page){
        (new CategotyValidate())->goCheck();
        $res = MenuModel::getAllMenuInCategory($categoryID,$page);
        if($res->isEmpty()){
            $res=[];
        }
        return $res;

    }

    /**
     * @param $id 菜谱id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\Exception
     * 单个菜谱详情
     */
    public function getOneMenu($id){
        (new IDMustBePositiveInt())->goCheck();
        $res = MenuModel::getSingleMenu($id);
        if($res->isEmpty()){
            throw new ProductException();
        }

        return $res;
    }

    public function getMainProductInfo($id){
        (new IDMustBePositiveInt())->goCheck();
        $res = MenuModel::getMainProductByMenu($id);
        if($res->isEmpty()){
            throw new ProductException();
        }
        return $res;
    }
}
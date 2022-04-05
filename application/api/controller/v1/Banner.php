<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 2:51
 */

namespace app\api\controller\v1;


use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;
use app\api\controller\BaseController;
use think\Request;

class Banner extends BaseController
{
    /**
     * @param $id 什么位置的Banner图
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws BannerMissException
     * @throws \think\Exception
     * 轮播图
     */
    public function getBanner($id){


        print_r(555);die;
        (new IDMustBePositiveInt())->goCheck();
        $banner = BannerModel::getBannerByID($id);
        return $banner;
//        return [
//            'code' =>200,
//            'msg'=>'success',
//            'data' =>$banner
//        ];
    }
}
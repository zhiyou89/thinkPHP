<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:35
 */

namespace app\api\controller\v1;



use app\api\controller\BaseController;
use app\api\model\Setting as SettingModel;
use think\Db;

class Setting extends BaseController
{
    public function showSetting(){
  //      "a:5:{i:0;s:47:\"1.隔天配送商品比即时配送商品优惠\";i:1;s:43:\"2.商城试业期间满39元配送费全免\";i:2;s:61:\"3.隔天配送：当天晚上24:00前下单，第二天配送\";i:3;s:60:\"4.下单服务时间为9:00-21:00，平均一小时内送达\";i:4;s:36:\"5.配送范围：市府方圆6公里\";}"
//        $arr = [
//            0=> '1.隔天配送商品比即时配送商品优惠',
//            1=>'2.商城试业期间满39元配送费全免',
//            2=>'3.隔天配送：当天晚上24:00前下单，第二天配送',
//            3=>'4.下单服务时间为9:00-21:00，平均一小时内送达',
//            4=>'5.配送范围：市府方圆6公里'
//        ];
//        $arr = json_encode($arr);
//        SettingModel::where('id',1)->update([
//            'notice_content'=>$arr
//        ]);
//        return $arr;
        return SettingModel::showSettings();
    }
}
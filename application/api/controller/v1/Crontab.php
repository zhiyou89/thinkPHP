<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 22:57
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Crontab as CrontabModel;
use app\api\model\UserCoupon;
use app\api\model\ParcelLog;

class Crontab extends BaseController
{

    //每天定时改变没有付款的订单转态
    public function updateOrderStatus(){
        $CrontabModel = new CrontabModel();
        $CrontabModel->updateOrderStatusEveryDay();
    }

    //每天定时将付完款的订单修改我为订单状态为3，同时订单快照里面的商品状态修改为3
    public function updateFinishOrder(){
        $CrontabModel = new CrontabModel();
        $CrontabModel->updateFinishOrderStatus();
    }

    //十二点将当天没有用的券隐藏
    public function updateCoupon(){
        $userCouponModel = new UserCoupon();
        $ids = UserCoupon::whereTime('create_time','Today')->where('coupon_id',1)->field('id')->select()->toArray();
        foreach ($ids as &$v){
            $v['show'] = 0;
        }
        $userCouponModel->saveAll($ids);
    }

    /**
     * 找出两天前的记录
     *
     */
    public function getFenyong(){
        $strs="QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm";
        $str = substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),4);
        $strNumber = time();
        return $str.'_'.$strNumber;
    }
}

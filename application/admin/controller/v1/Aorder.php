<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller\v1;



use app\admin\controller\Base;
use app\admin\model\Order as AOrderModel;
use app\api\validate\IDMustBePositiveInt;

class Aorder extends Base
{
    public function AllOrder(){
//        (new IDMustBePositiveInt())->goCheck();
        $res = AOrderModel::getOrderByWhere();
        return [
            'status'=>200,
            'msg'=>'success',
            'data'=>$res['data'],
            'count'=>$res['count'],
            'current'=>$res['current']
        ];
    }
}

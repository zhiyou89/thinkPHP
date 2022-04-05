<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:28
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\server\Pay as PayService;
use app\api\server\WxNotify as WxNotifyService;
class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    public function getPreOrder($id='')
    {
       
        (new IDMustBePositiveInt()) -> goCheck();
        $pay= new PayService($id);
        return $pay->pay();
    }

    public function receiveNotify(){
        $notify= new WxNotifyService();
        $notify->Handle();
    }

}
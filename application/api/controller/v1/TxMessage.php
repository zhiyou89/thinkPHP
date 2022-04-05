<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\server\TxCms;
class TxMessage extends BaseController
{
    public function sendCms(){
        $service = new TxCms();
        $code = '8618666261519';
        $service->sendCms($code);
    }
}
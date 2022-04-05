<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\server\AccessToken as AccessTokenservice;

class AccessToken extends BaseController
{
    public function getAccessToken(){
       $accessService =new  AccessTokenservice();
       return $accessService->get();
   }
}

<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\controller;
use think\Controller;
use app\gzh\service\AccessToken;

class Token extends Controller
{
    public function accessToken()
    {
        $accessTokenService = new AccessToken();
       $res =   $accessTokenService->getSignPackage();
       return $res;
    }

}
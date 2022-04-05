<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\controller;
use app\gzh\service\Token as TokenService;
use app\gzh\service\AccessToken as AccessTokenService;
use my\Redis as RedisService;
class Index
{
    public function getAccessToken(){
        $accessToken = new AccessTokenService();
        return $accessToken->getUserInfo();
    }

    public function getScope(){
        $accessToken = new AccessTokenService();
        return $accessToken->getScope();
    }

    public function checkToken(){
        $token = new TokenService();
        $token->valid();
    }

//    public function getScop(){
//        $accessToken = new AccessTokenService();
//        return $accessToken->getScope();
//        //scope=snsapi_userinfo实例
//        $appid=config('wx.app_id');
//        $redirect_uri = urlencode ( 'https://www.yjcloudcomputing.com/public/index.php/gzh/access_token' );
//        $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
//        print_r($url);die;
//    }
}
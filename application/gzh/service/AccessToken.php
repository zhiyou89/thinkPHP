<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\gzh\service;

use app\gzh\model\BUsers;
use think\Cache;

class AccessToken
{

    public function getScope(){
        $appid=config('wx.gzh_app_id');
        $redirect_uri = urlencode ( 'https://www.yjcloudcomputing.com/store/index.html' );
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        return $url;
    }

    public function getUserInfo(){
        if(!isset($_GET["code"])){
            return false;
        }
        $appid = config('wx.gzh_app_id');
        $secret = config('wx.gzh_app_secret');
        $code = $_GET["code"];

//第一步:取得openid
        $oauth2Url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';;
        $token = json_decode(file_get_contents($oauth2Url));
        $openid= $token->openid;
        $access_token = $token->access_token;
//第二步:根据全局access_token和openid查询用户信息
        $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'⟨=zh_CN';
        $info = json_decode(file_get_contents($info_url));
        $res = BUsers::logIn($info);
        if($res){
            header('location:https://www.yjcloudcomputing.com/store/index.html');
        }
    }


    /**
     * 全局
     */
    public function getAccessToken(){
        $accesstToken = '';
        $accesstToken = Cache::get('access_token');
        if(!$accesstToken){
            $appid=config('wx.gzh_app_id');
            $secret = config('wx.gzh_app_secret');
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
            $array = curl_get($url);
            $accesstToken = $array['access_token'];
            Cache::set('access_token',$accesstToken,7000);
        }
        return $accesstToken;
    }

    public function jsapiTicket(){
        $ticket = '';
        $ticket = Cache::get('jsapi_ticket');
        if(!$ticket){
            $access_token = $this->getAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $array = curl_get($url);
            $ticket = $array['ticket'];
            Cache::set('jsapi_ticket',$ticket,7000);
        }
        return $ticket;
    }

    public function getSignPackage(){
        $assessToken = $this->getAccessToken();
        $ticket = $this->jsapiTicket();
        $url = "https://www.yjcloudcomputing.com/store/pre_order.html";
        $timestamp = time();
        $nonceStr = getRandChar(16);

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序 j -> n -> t -> u
        $string = "jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        return [
            'timestamp'=>$timestamp,
            'nonce_str'=>$nonceStr,
            'signature'=>$signature,
        ];
    }




}
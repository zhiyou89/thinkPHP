<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:58
 */

return [
    //腾讯云(短信需要用到)
    "secret_id"=>"AKIDeBTumb2XQUrkdJv2C5h24uXw5pGSakyS",
    "secret_key"=>"OXVCepPs76oEOJ1frqGPIvKRO3TdVO1M",
    //小程序的
    "app_id"=>"wxc50929e6bdd10304",
    "app_secret"=>"b5c2abd10c8c96456223d279d89eb204",
    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
    //获取code
    'get_code_url' => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect",
    // 微信获取access_token的url地址
    'access_token_url' => " https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code",


    'gzh_app_id'=>"wx558112285c3e2ac1",
    'gzh_app_secret'=>'ba544169322a5f5ad2bba215036d018b',

    'token'=>'ghfgijkliohj55486221',


    //获取微信小程序码链接

    'get_code_by_wx_url'=>"https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s"
];
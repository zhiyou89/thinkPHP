<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */

namespace app\api\server;
use app\api\server\AccessToken;

class SendWxMessage
{
    public function SendTemplateMessage(){
        $AccessToken = new AccessToken();
        $touser = 'osgr45a096mqArHRVy96K2KGANTM';
        $accessToken = $AccessToken->get();
        $templateid = 'SDe4C26WvMEEVhYwz36slL-PssgF8d3Z_Z-Gk_0VnQs';


        $url="https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=".$accessToken;
        $data=[
            'touser'=>$touser,
            'template_id'=>$templateid,

            'data'=>array(
                'keyword1' => array('value' =>'美如斯沙发'),
                'keyword2' => array('value' =>'车市'),
            ),
        ];
//        $data=json_encode($data);
        $result = curl_post($url,$data);
        echo $result;
    }

}

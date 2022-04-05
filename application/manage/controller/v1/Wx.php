<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 21:48
 */

namespace app\manage\controller\v1;

use think\Controller;
use app\api\server\AccessToken as AccessTokenService;
use  think\Cache;



class Wx extends Controller
{
    public function PushMessage(){
        $AccessTokenService =new AccessTokenService();
        $access =  $AccessTokenService->get();
        $json_template = $this->json_tempalte();
        $url="https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token".$access;
        $res=$this->curl_post($url,urldecode($json_template));
        return $res;

    }

    public function json_tempalte(){

        //模板消息
        $template=array(
            'touser'=>'osgr45a096mqArHRVy96K2KGANTM', //用户openid
            'template_id'=>"SDe4C26WvMEEVhYwz36slKH8P8a27V5iya1aW2KqcFo", //在公众号下配置的模板id
            'url'=>"/pages/index/index", //点击模板消息会跳转的链接
            'data'=>array(
                'character_string6'=>array('value'=>urlencode('测试文章标题'),'color'=>'#FF0000'), //keyword需要与配置的模板消息对应
                'thing1'=>array('value'=>urlencode('测试文章标题'),'color'=>'#FF0000'),
                'amount2'=>array('value'=>urlencode('测试发布人'),'color'=>'#FF0000'),
                'time4'=>array('value'=>urlencode(date("Y-m-d H:i:s")),'color'=>'#FF0000'),
                'number3'=>array('value'=>urlencode('1'),'color'=>'#FF0000'),
                 )

        );
        $json_template=json_encode($template);
        return $json_template;
    }

    function curl_post($url , $data=array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }

}
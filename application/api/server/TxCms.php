<?php
namespace app\api\server;
use think\Loader;
Loader::import('vendor.autoload',EXTEND_PATH,'.php');
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20190711\SmsClient;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;

class TxCms{
    public function sendCms($code){
        try {
            $SecretId = config('wx.secret_id');
            $secret_key = config('wx.secret_key');
            $cred = new Credential($SecretId, $secret_key);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, "", $clientProfile);

            $req = new SendSmsRequest();

            $params = array(
                "PhoneNumberSet" => array('8618666261519','8613450512817'),
                "TemplateID" => "936400",
                "Sign" => "篮蜂商城",
                "SmsSdkAppid" => "1400487593"
            );
            $req->fromJsonString(json_encode($params));

            $resp = $client->SendSms($req);

            print_r($resp->toJsonString());
        }
        catch(TencentCloudSDKException $e) {
            echo $e;
        }
    }
}

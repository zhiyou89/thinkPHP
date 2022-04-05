<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Loader;
use think\Request;
Loader::import('CosSdkV5.vendor.autoload',EXTEND_PATH,'.php');
class Uploads extends BaseModel
{
    public static function uploadFile(){
        $file = request()->file('file');
//        print_r( $file->getInfo());die;
        $cosClient = new \Qcloud\Cos\Client(
            array(
                'region' => 'ap-guangzhou',
                'schema' => 'https',
                'credentials' => array(
                    'secretId' => 'AKIDeBTumb2XQUrkdJv2C5h24uXw5pGSakyS',
                    'secretKey' =>'OXVCepPs76oEOJ1frqGPIvKRO3TdVO1M',
                )
            )
        );


        try {
            $info = $file->getInfo();
            $str = date("Y-m-d") . "/" . md5(microtime()) .$info['name'];
            $key = 'new/'.$str;
            $result = $cosClient->putObject(array(
                'Bucket' => 'youguan-1257044613', //格式：BucketName-APPID
                'Key' =>$key,
                'Body' => fopen($info['tmp_name'], 'rb'),
            ));
            // 请求成功
            return json($str,200);
        } catch (\Exception $e) {
            // 请求失败
            echo($e);
        }
    }


}
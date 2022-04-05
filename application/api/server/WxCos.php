<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */
namespace app\api\server;

use think\Loader;

Loader::import('CosSdkV5.vendor.autoload',EXTEND_PATH,'.php');
class WxCos
{

    private $cosClient;
    function __construct()
    {
        $this->cosClient = new \Qcloud\Cos\Client(
            array(
                'region' => 'ap-guangzhou',
                'schema' => 'https',
                'credentials' => array(
                    'secretId' => 'AKIDeBTumb2XQUrkdJv2C5h24uXw5pGSakyS',
                    'secretKey' =>'OXVCepPs76oEOJ1frqGPIvKRO3TdVO1M',
                )
            )
        );
    }

    public function createBucket(){

        try {
            $bucket = "ygshopd-1257044613"; //存储桶名称 格式：BucketName-APPID
            $result = $this->cosClient->createBucket(array('Bucket' => $bucket));
            //请求成功
            print_r($result);
        } catch (\Exception $e) {
            //请求失败
            print_r($e);
        }
    }
    public function showListBuckets(){
        try {
            //请求成功
            $result = $this->cosClient->listBuckets();
            print_r($result);
        } catch (\Exception $e) {
            //请求失败
            echo($e);
        }
    }
    public function upload($url){


        try {
            $result = $this->cosClient->putObject(array(
                'Bucket' => 'ygshopd-1257044613', //格式：BucketName-APPID
                'key' =>date("Y-m-d") . "/" . md5(microtime()) . '.jpg',
                'Body' => fopen($url, 'rb'),
            ));
            // 请求成
            print_r($result);
        } catch (\Exception $e) {
            echo "$e\n";
        }

    }
}
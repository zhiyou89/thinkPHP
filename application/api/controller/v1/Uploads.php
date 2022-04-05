<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\server\WxCos as wxCosService;
use think\Loader;

Loader::import('CosSdkV5.vendor.autoload',EXTEND_PATH,'.php');
class Uploads extends BaseController{

    public function uploadImg(){
        $file = request()->file('file');
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
            $str = date("Y-m-d") . "/" . md5(microtime()) . '.jpg';
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






//        $file = request()->file('file');
//        // 移动到框架应用根目录/public/uploads/ 目录下
//        if($file){
//            $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
//            if($info){
//                return $info->getFilename();
//            }else{
//                // 上传失败获取错误信息
//                return json($file->getError());
//            }
//        }
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller\v1;



use app\admin\controller\Base;
use app\admin\model\Uploads as UploadsModel;
use think\Request;


class Aupload extends Base
{
    public function UploadFile(){

        $res = UploadsModel::uploadFile();
        return $res;
    }

    public function uploadProductImage(){
        $request = Request::instance();
        $param = $request->param();
        return UploadsModel::uploadFile();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller\v1;



use app\admin\controller\Base;
use app\admin\model\Product;
use app\admin\model\Excel as ExcelModel;
use app\admin\model\Uploads as UploadsModel;

class Excel extends Base
{
    public function insertProductSqlData(){
//        $fileName = UploadsModel::uploadFile();
        $path  = ROOT_PATH . 'public' . DS . 'sql/product.xlsx';
//        if($fileName['statusCode']==200){
//            $path  = ROOT_PATH . 'public' . DS . 'sql/'.$fileName['data'];
//        }else{
//            return [
//                'statusCode'=>400,
//                'msg'=>'error',
//
//                'data'=>$fileName
//            ];
//        }

        $res = ExcelModel::insertAllForProduct($path);
        if($res){
            return [
                'statusCode'=>200,
                'msg'=>'success',
                'data'=>$res
            ];
        }else{
            return[
                'statusCode'=>400,
                'msg'=>'error',
                'data'=>$res
            ];
        }

    }

    //写入sql  表image

    public function insertSqlToImage(){
//        $fileName = UploadsModel::uploadFile();
//        if($fileName['statusCode']==200){
//            $path  = ROOT_PATH . 'public' . DS . 'sql/'.$fileName['data'];
//        }else{
//            return [
//                'statusCode'=>400,
//                'msg'=>'error',
//
//                'data'=>$fileName
//            ];
//        }
        $path  = ROOT_PATH . 'public' . DS . 'sql/image.xlsx';
        $res = ExcelModel::insertAllForImage($path);
        if($res){
            return [
                'statusCode'=>200,
                'msg'=>'success',
                'data'=>$res
            ];
        }else{
            return[
                'statusCode'=>500,
                'msg'=>'success',
                'data'=>$res
            ];
        }
    }

    public function insertSqlToProductImage(){
        $path  = ROOT_PATH . 'public' . DS . 'sql/product_image.xlsx';
//        $fileName = UploadsModel::uploadFile();
//        if($fileName['statusCode']==200){
//            $path  = ROOT_PATH . 'public' . DS . 'sql/'.$fileName['data'];
//        }else{
//            return [
//                'statusCode'=>400,
//                'msg'=>'error',
//
//                'data'=>$fileName
//            ];
//        }
        $res = ExcelModel::insertAllForProductImage($path);

        if($res){
            return [
                'statusCode'=>200,
                'msg'=>'success',
                'data'=>$res
            ];
        }else{
            return[
                'statusCode'=>500,
                'msg'=>'success',
                'data'=>$res
            ];
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Request;
use think\Loader;
use think\Db;

Loader::import('PHPExcel.Classes.PHPExcel',EXTEND_PATH,'.php');

class Excel extends BaseModel
{
    public static function getExcelData($fileName){
//        $path  = ROOT_PATH . 'public' . DS . 'sql/order.xlsx';
        $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
        $obj_PHPExcel =$objReader->load($fileName, $encode = 'utf-8');
        $excel_array=$obj_PHPExcel->getsheet(0)->toArray();
        array_shift($excel_array);
        return $excel_array;
    }

    public static function insertAllForProduct($fileName){
        $excel_array = self::getExcelData($fileName);
        $data = [];
        foreach ($excel_array as $k=>$v){
            $data[$k]['name'] = $v[0];
            $data[$k]['product_describe'] =$v[1];
            $data[$k]['market_price'] = $v[2];
            $data[$k]['price'] =  $v[3];
            $data[$k]['stock']=$v[4];
            $data[$k]['category_id'] = $v[5];
            $data[$k]['index_category_id'] = $v[6];
            $data[$k]['hot_rank'] = $v[7];
            $data[$k]['main_img_url'] = $v[8];
            $data[$k]['product_best_descript'] = $v[9];
            $data[$k]['make_in'] = $v[10];
            $data[$k]['create_time'] = time();
        }
        $res = Db::table('product')->insertAll($data);
        return $res;
    }

    public static function insertAllForImage($fileName){
        $excel_array = self::getExcelData($fileName);

        $data = [];
        foreach ($excel_array as $k=>$v){
            $data[$k]['url'] = $v[0];
            $data[$k]['describe'] = $v[1];
            $data[$k]['from'] = 2;
        }
        $res = Db::table('image')->insertAll($data);
        return $res;
    }

    public static  function insertAllForProductImage($fileName){
        $excel_array = self::getExcelData($fileName);
        $data = [];
        foreach ($excel_array as $k=>$v){
            $data[$k]['img_id'] = $v[0];
            $data[$k]['banner_product_id'] = $v[1];
            $data[$k]['product_id'] = $v[2];
        }
        $res = Db::table('product_image')->insertAll($data);
        return $res;
    }
}
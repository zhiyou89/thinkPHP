<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Exception;
use think\Log;
use think\Request;
use think\Loader;
use think\Db;
use app\admin\model\Image as imageModel;
use app\admin\model\MenuImage as MenuImageModel;


class Menu extends BaseModel
{
    public function getMenuItemsAttr($value)
    {
        $res= json_decode($value);
        return $res;
    }

    public static function getAllMenu(){
        $res = [];
       $request = Request::instance();
       $param = $request->param();
       $where = [];
       $res['data'] = self::where($where)
           ->order('id desc')
           ->page($param['page'],10)
           ->select();
       $res['count'] = self::count();
       $res['current'] = $param['page'];
       return $res;
    }

    public static function updateMenuInfo(){
        $request = Request::instance();
        $param = $request->param();
        $info = [];
        $items = [];
        array_push($items,$param['items1'],$param['items2'],$param['items3'],$param['items4']);
        $items = json_encode($items);
        $info['menu_hot'] = $param['order'];
        $info['menu_name'] = $param['menu_name'];
        if($param['main_img']){
            $info['menu_img_or_vedio_url'] = 'https://youguan-1257044613.cos.ap-guangzhou.myqcloud.com/new/'.(rtrim($param['main_img'], ','));
        }
        $info['menu_items'] = $items;
        if(isset($param['mid'])){
            Db::startTrans();
            try{
                if($param['detail_img']){
                    $detail = rtrim($param['detail_img'], ',');
                    $detailArr = explode(',',$detail);
                    MenuImageModel::destroy(['menu_id' => $param['mid']]);
                    foreach ($detailArr as $k=>$v){
//                        $img = [];
//                        $img['url'] = $v;
//                        $img['from'] = 2;
                        $imgRes = imageModel::create($param['img'][$k]);
                        $order = substr($v,-5,1);
                        if(!is_numeric($order)){
                            return json([
                                'statusCode'=>404,
                                'msg'=>'图片路径出错，请按照约定的图片路径'
                            ]);
                        }else{
                            MenuImageModel::create([
                                'img_id'=>$imgRes->id,
                                'order' =>$order,
                                'menu_id'=>$param['mid']
                            ]);
                        }

                    }
                }
                $res = self::where('id','=',$param['mid'])
                    ->update($info);
                Db::commit();
                return json([
                    'statusCode'=>200,
                    'msg'=>'操作成功'
                ]);
            }catch (Exception $e){
                return [
                    'statusCode'=>404,
                    'msg'=>'系统错误'
                ];
                Db::rollback();
                Log::error($e);
            }

        }else{

            Db::startTrans();
            try{
                $detail = rtrim($param['detail_img'], ',');
                $detailArr = explode(',',$detail);
                foreach ($detailArr as $k=>$v){
                    $imgRes = imageModel::create($param['img'][$k]);
                    $order = substr($v,-5,1);
                    if(!is_numeric($order)){
                        return json([
                            'statusCode'=>404,
                            'msg'=>'图片路径出错，请按照约定的图片路径'
                        ]);
                    }else{
                        $res = self::create($info);
                        MenuImageModel::create([
                            'img_id'=>$imgRes->id,
                            'order' =>$order,
                            'menu_id'=>$res->id
                        ]);
                    }

                }
                Db::commit();
                return json([
                    'statusCode'=>200,
                    'msg'=>'操作成功'
                ]);
            }catch (Exception $e){
                Db::rollback();
                Log::error($e);
                return json([
                    'statusCode'=>404,
                    'msg'=>'系统错误'
                ]);
            }

        }

    }


}
<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Db;
use think\Exception;
use think\Log;
use think\Request;
use app\admin\model\Image as imageModel;
use app\admin\model\ProductImage as productImageModel;
class Product extends BaseModel
{
    public function index(){
        echo 99;
    }

    public function productImgs(){
        return  $this->hasMany('ProductImage','product_id','id');
    }

    public function banners(){
        return $this->hasMany('ProductImage', 'banner_product_id', 'id');
    }
    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }



    public function tags(){
        return $this->belongsToMany('tag', 'product_tag', 'tag_id', 'product_id');
    }

    protected function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }
    protected function getOnSellAttr($value,$data){
        return $this->prefixOnSell($value,$data);
    }

    public static function getAllProduct(){
        $where = [];
        $request = Request::instance();
        $param = $request->param();
        if(isset($param['product'])&&$param['product']){
            $wher['name'] = $param['product'];
        }
       $data = self::where($where)
           ->order('create_time desc')
           ->page($param['page'],10)
           ->select();
        $res['data'] = $data;
       $res['count'] = self::count();
       $res['current'] = $param['page'];
       return $res;
    }

    public static function onSell(){
        $request = Request::instance();
        $param = $request->param();
        $onSell = 0;
        if($param['onsell']==0){
            $onSell = 1;
        }
        return self::update([
            'id'=> $param['id'],
            'onsell'=> $onSell
        ]);
    }

    //获取单个商品
    public static function getOneProduct(){
        $request = Request::instance();
        $param = $request->param();
        $data =  self::with([
            'productImgs' => function($query){
                $query->with('imgUrl')->order('order','asc');
            }
        ])
            ->with([
                'banners' => function($query){
                    $query->with('imgUrl');
                }
            ])
            ->with(['properties','tags'])

            ->find($param['id']);
        return $data;
    }

    public static function saveProductData(){
        $request = Request::instance();
        $param = $request->param();

        $data = [];
        $data['name'] = $param['name'];
        $data['product_describe'] = $param['name'];
        $data['category_id'] = $param['categoryid'];
        $data['index_category_id'] = $param['deliveryid'];
        $data['product_best_descript'] = $param['sname'];
        $data['price'] = $param['price'];
        $data['market_price'] = $param['market_price'];
        $data['stock'] = $param['stock'];
        $data['hot_rank'] = $param['order'];
        if($param['main_img']){
            $data['main_img_url'] = rtrim($param['main_img'], ',');
        }
        $data['from'] = 2;
        $data['create_time'] = time();
        $data['make_in'] = $param['make_in'];
        $data['onsell'] = 1;
        if($param['pid']){
            //商品详情图
            Db::startTrans();
            try{
                if($param['detail_img']){
                    $detail = rtrim($param['detail_img'], ',');
                    $detailArr = explode(',',$detail);
                    productImageModel::destroy(['product_id' => $param['pid']]);
                    foreach ($detailArr as $v){
                        $img = [];
                        $img['url'] = $v;
                        $img['from'] = 2;
                        $imgRes = imageModel::create($img);
                        $order = substr($v,-5,1);
                        if(!is_numeric($order)){
                            return json([
                                'statusCode'=>404,
                                'msg'=>'图片路径出错，请按照约定的图片路径'
                            ]);
                        }else{
                            productImageModel::create([
                                'img_id'=>$imgRes->id,
                                'order' =>$order,
                                'product_id'=>$param['pid']
                            ]);
                        }

                    }
                }
                //商品轮播图
                if($param['banner']){
                    $banner = rtrim($param['banner'], ',');
                    $bannerArr = explode(',',$banner);
                    productImageModel::destroy(['banner_product_id' => $param['pid']]);
                    foreach ($bannerArr as $v1){
                        $bimg = [];
                        $bimg['url'] = $v1;
                        $bimg['from'] = 2;
                        $bimgRes = imageModel::create($bimg);
                        $border = substr($v1,-5,1);
                        if(!is_numeric($border)){
                            return json([
                                'statusCode'=>404,
                                'msg'=>'图片路径出错，请按照约定的图片路径'
                            ]);
                        }else{
                            productImageModel::create([
                                'img_id'=>$bimgRes->id,
                                'order' =>$border,
                                'banner_product_id'=>$param['pid']
                            ]);
                        }

                    }
                }
                $res = self::where('id','=',$param['pid'])
                    ->update($data);
                Db::commit();
                return json([
                    'statusCode'=>200,
                    'msg'=>'操作成功'
                ]);
            }catch (Exception $e){
                Db::rollback();
                Log::error($e);
            }


        }else{
            Db::startTrans();
            try{
                $res = self::create($data);
                $id = $res->id;
                //商品详情图
                if($param['detail_img']){
                    $detail = rtrim($param['detail_img'], ',');
                    $detailArr = explode(',',$detail);
                    productImageModel::destroy(['product_id' => $id]);
                    foreach ($detailArr as $v){
                        $img = [];
                        $img['url'] = $v;
                        $img['from'] = 2;
                        $imgRes = imageModel::create($img);
                        $order = substr($v,-5,1);
                        if(!is_numeric($order)){
                            return json([
                                'statusCode'=>404,
                                'msg'=>'图片路径出错，请按照约定的图片路径'
                            ]);
                        }else{
                            productImageModel::create([
                                'img_id'=>$imgRes->id,
                                'order' =>$order,
                                'product_id'=>$id
                            ]);
                        }

                    }
                }
                //商品轮播图
                if($param['banner']){
                    $banner = rtrim($param['banner'], ',');
                    $bannerArr = explode(',',$banner);
                    productImageModel::destroy(['banner_product_id' => $id]);
                    foreach ($bannerArr as $v1){
                        $bimg = [];
                        $bimg['url'] = $v1;
                        $bimg['from'] = 2;
                        $bimgRes = imageModel::create($bimg);
                        $border = substr($v1,-5,1);
                        if(!is_numeric($border)){
                            return json([
                                'statusCode'=>404,
                                'msg'=>'图片路径出错，请按照约定的图片路径'
                            ]);
                        }else{
                            productImageModel::create([
                                'img_id'=>$bimgRes->id,
                                'order' =>$border,
                                'banner_product_id'=>$id
                            ]);
                        }

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
            }


        }
    }


}
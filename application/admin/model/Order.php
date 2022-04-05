<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Request;

class Order extends BaseModel
{

    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return [];
        }
        return json_decode($value);
    }

    public function getStatusAttr($value){
        $status = '';
        if($value==1){
            $status = '未付款';
        }elseif ($value==2){
            $status = '已付款';
        }elseif ($value==3){
            $status = '已完成';
        }elseif ($value==4){
            $status = '退款中';
        }elseif($value==6){
            $status = '退款完成';
        }
        return $status;
    }

    public function getSnapDeliveryAttr($value){
        if(empty($value)){
            return [];
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return [];
        }
        return json_decode(($value));
    }
    public static function getOrderByWhere(){
        $request = Request::instance();
        $res = [];

        $where = [];
        $param = $request->param();
        $res['current'] = $param['page'];
        //商品标题
        if($param['selectval']==1){
            if($param['text']){
                $res['data'] =  self::where('snap_name','like','%'.$param['text'].'%')
                    ->order('create_time desc')
                    ->page($param['page'],20)
                    ->select();
                $res['count'] = self::where('snap_name','like','%'.$param['text'].'%')->count();
            }else{
                $res['data'] = self::order('create_time desc')
                    ->page($param['page'],20)
                    ->select();
                $res['count'] = self::count();
            }
        }

        //订单转态

        if($param['selectval']==2){
            if($param['number']){
                $res['data'] = self::where('order_no','=',$param['number'])
                    ->page($param['page'],20)
                    ->select();
                $res['count'] = self::where('order_no','=',$param['number'])->count();
            }
        }

        if($param['selectval']==3){
            if($param['status']){
                $res['data'] = self::where('status','=',$param['status'])
                    ->order('create_time desc')
                    ->page($param['page'],20)
                    ->select();
                $res['count'] = self::where('status','=',$param['status'])->count();
            }else{
                $res['data'] = self::order('create_time desc')
                    ->page($param['page'],20)
                    ->select();
                $res['count'] = self::count();
            }
        }

        //下单时间1608545723  1608739200
        if($param['selectval']==4){
            if($param['logmin'] && $param['logmax']){
                $logmin =strtotime(date($param['logmin']));
                $logmax = strtotime(date($param['logmax']))+24*60*60;
                $res['data'] = self::where('create_time','between',[$logmin,$logmax])
                    ->order('create_time desc')
                    ->page($param['page'],20)
                    ->select();
                $res['count'] = self::where('create_time','between',[$logmin,$logmax])->count();
           }
        }

        return $res;

    }

    public static function getSnapProduct(){
        $request = Request::instance();
        $param = $request->param();

        return self::where('id','=',  $param['id'])->find();
    }

}
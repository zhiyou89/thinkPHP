<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:35
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\BProduct;
use app\api\model\BOrder;
use app\api\model\BAddress;
use app\api\model\BUsers as BUsersModel;
use app\api\validate\Store as StoreValidate;
use app\api\validate\IDMustBePositiveInt;
use think\Cache;

class Store extends BaseController
{
    public function getStoreProductData($id,$page){
        (new StoreValidate())->goCheck();
        $res = BProduct::getProductData($id,$page);
        if(!empty($res)){
            return [
                'code'=>200,
                'data'=>$res
            ];
        }else{
            return [
                'code'=>404
            ];
        }
    }

    public function getStoreCategory(){
        return db('b_category')
            ->select();
    }

    public function getSearchInfo(){
        $str = input('str');
        return BProduct::searchGoods($str);
    }

    public function logIn(){
        $phone = input('phone');
        $psw = md5(input('password'));
        $res = BUsersModel::where('phone',$phone)
            ->find();
        if(empty($res)){
            $ress = BUsersModel::create([
                'phone'=>$phone,
                'psw'=>$psw
            ]);
            if(empty($ress)){
                return [
                    'code'=>400,
                    'msg'=>'登录失败'
                ];
            }else{
                $key = BUsersModel::saveCache($phone,$res->id);
                if($key){
                    return [
                        'code'=>200,
                        'msg'=>'登录成功',
                        'data'=>$key
                    ];
                }else{
                    return [
                        'code'=>400,
                        'msg'=>'登录失败'
                    ];
                }

            }
        }else{
            if($psw != $res->psw){
                return [
                    'code'=>400,
                    'msg'=>'密码错误'
                ];
            }else{
                $key = BUsersModel::saveCache($phone,$res->id);
                if($key){
                    return [
                        'code'=>200,
                        'msg'=>'登录成功',
                        'data'=>$key
                    ];
                }else{
                    return [
                        'code'=>400,
                        'msg'=>'登录失败'
                    ];
                }
            }

        }
    }



    public function getCache(){
        $cache = input('cache');
        $res = Cache::get($cache);
        if($res){
            return [
                'code'=>200,
                'msg'=>'success'
            ];
        }else{
            return [
                'code'=>400,
                'msg'=>'fail'
            ] ;
        }
    }

    
    public function cartButton(){
        return BUsersModel::cartButton(input('type'),input('id'));
    }

    public function addCart(){
        $id =input('id');
        $res = BUsersModel::saveCart($id);
        if($res){
            return [
                'code'=>200,
                'msg'=>'加入购物车成功'
            ];
        }else{
            return [
                'code'=>400,
                'msg'=>'登录过期，请重新登录'
            ];
        }

    }

    public function getCartCounts(){

        $res = BUsersModel::getCartCounts();
        return $res;
    }


    public function updateAttrInCart(){
        $type = input('type');
        $num = input('id');
        return BUsersModel::updateAttrInCart($type,$num);
    }
    public function showCart(){
        return BUsersModel::showCart();
    }

    public function cartChecked(){
        $res = BUsersModel::cartChecked();
        return [
            'code'=>200,
            'msg'=>'success',
            'data'=>$res
        ];
    }

    public function pay(){
        return BUsersModel::pay();
    }

    public function orderInfo(){
        return BOrder::getOrderInfo();
    }

    public function getOneOrder(){
        return BOrder::getOneOrder();
    }

    public function cancelOrder(){
        return BOrder::cancelOrder();
    }

    public function getGoodInfo(){
        $id = input('good_id');
        return BProduct::where('id',$id)->find();
    }

    public function setAddressToCache(){
        $arr = [];
        $address1 = input('address1');
        $address2 =input('address2');
        $name = input('name');
        $phone = input('phone');
        $sex = input('sex');
        $arr = [
            'id'=>time(),
            'address1'=>$address1,
            'address2'=>$address2,
            'name'=>$name,
            'phone'=>$phone,
            'set'=>$sex
        ];
        return BAddress::setAddress($arr);
    }

    public function getAddressToCache(){
        return BAddress::getAddress();
    }

    public function getOneAddress(){
        $ID = input('id');
        return BAddress::getOneAddress($ID);
    }

    public function editAddress(){
        $arr = [];
        $address1 = input('address1');
        $address2 =input('address2');
        $name = input('name');
        $phone = input('phone');
        $sex = input('sex');
        $arr = [
            'id'=>time(),
            'address1'=>$address1,
            'address2'=>$address2,
            'name'=>$name,
            'phone'=>$phone,
            'set'=>$sex
        ];
        $ID = input('id');
        return BAddress::editAddress($arr,$ID);
    }

    public function delAddress(){
        $id = input('id');
       return BAddress::delAddress($id);
    }

    public function chooseAddress(){
        $id = input('id');
        return BAddress::chooseAddress($id);
    }

}
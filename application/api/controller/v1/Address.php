<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\api\controller\v1;


use app\api\validate\AddressNew;

class Address
{
    public function creatOrUpdateAddress(){
        (new AddressNew())->goCheck();

    }
}
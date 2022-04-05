<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/14
 * Time: 0:21
 */

namespace app\api\validate;


class Order extends BaseValidate
{
    protected $rule = [
        'page' => 'require|isPositiveInteger',
        'status' =>'require|isPositiveInteger'

    ];
    protected $message = [
        'page' => 'page必须是正整数',
        'status' => 'categoryID必须是正整数'
    ];

}
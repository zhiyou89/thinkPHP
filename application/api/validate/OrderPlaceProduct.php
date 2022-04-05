<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/15
 * Time: 22:22
 */

namespace app\api\validate;


class OrderPlaceProduct extends BaseValidate
{
    protected $rule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
    ];

    protected $message = [
        'product_id' => "必须为正整数",
        'count' => "必须为正整数"
    ];

}
<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/13
 * Time: 17:12
 */

namespace app\api\validate;


class CategotyValidate extends BaseValidate
{
    protected $rule = [
        'page' => 'require|isPositiveInteger',
        'categoryID' =>'require|isPositiveInteger'
    ];
    protected $message = [
        'page' => 'id必须是正整数',
        'categoryID' => 'categoryID必须是正整数'
    ];
}
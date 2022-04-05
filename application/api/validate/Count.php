<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 21:57
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [

        'count' => 'isPositiveInteger|between:1,15'
    ];

    protected $message = [
        'count' => '1-15的整数啊大哥，不要搞搞震'
    ];
}
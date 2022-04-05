<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/4
 * Time: 22:27
 */
namespace app\api\controller;

use think\Controller;
use app\api\server\Token;

class BaseController extends Controller
{

    protected $data;
    protected $msg="操作成功";
    protected $code=200;
    /**
     * 检测是不是普通会员
     * @throws \app\lib\exception\ForbiddenException
     * @throws \app\lib\exception\TokenException
     */
    protected function checkExclusiveScope()
    {

        Token::needExclusiveScope();
    }

//    /**
//     * 检测是不是普通会员
//     * @throws \app\lib\exception\ForbiddenException
//     * @throws \app\lib\exception\TokenException
//     */
//    protected function checkPrimaryScope()
//    {
//        Token::needPrimaryScope();
//    }

    /**
     *检测是不是超级管理员
     */
    protected function checkSuperScope()
    {
        Token::needSuperScope();
    }

    protected function returnArrayType($data, $msg, $code=200)
    {
        $res = [];
        $res['code'] = $code;
        $res['msg'] = $msg;
        $res['data'] = $data;
        return $res;
    }

}
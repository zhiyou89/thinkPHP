<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:35
 */

namespace app\api\controller\v1;


use app\api\server\UserToken;
use app\api\validate\TokenGet;
use app\lib\exception\TokenException;
use app\api\server\Token as TokenService;

class Token
{
    /**
     * @param string $code
     * @return string
     * @throws TokenException
     * @throws \think\Exception
     * 生成令牌

     */
    public function getToken($code=''){
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $res = $ut->get($code);
        if(!$res){
            throw new TokenException();
        }
        return $res;
    }

    public function checkToken($token){
       $res =  TokenService::verifyToken($token);
       return $res;
    }
}
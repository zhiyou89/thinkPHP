<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 0:46
 */

namespace app\api\server;


use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use app\lib\enum\ScopeEnum;
use app\api\model\User;
use think\Exception;

class UserToken
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;
    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
    }




    public function get(){
        $res = curl_get($this->wxLoginUrl);
        $wxRes = json_decode($res, true);
        if(empty($wxRes)){
            throw new Exception('获取session_key及OPNEID时异常，微信内部错误');
        }else{
            $loginFail = array_key_exists('errcode',$wxRes);
            if($loginFail){
                $this->processLoginError($wxRes);
            }else{
               return $this->grantToken($wxRes);
            }
        }
    }


    // 颁发令牌
    // 只要调用登陆就颁发新令牌
    // 但旧的令牌依然可以使用
    // 所以通常令牌的有效时间比较短
    // 目前微信的express_in时间是7200秒
    // 在不设置刷新令牌（refresh_token）的情况下
    // 只能延迟自有token的过期时间超过7200秒（目前还无法确定，在express_in时间到期后
    // 还能否进行微信支付
    // 没有刷新令牌会有一个问题，就是用户的操作有可能会被突然中断
    private function grantToken($wxResult)
    {

        // 此处生成令牌使用的是TP5自带的令牌
        // 如果想要更加安全可以考虑自己生成更复杂的令牌
        // 比如使用JWT并加入盐，如果不加入盐有一定的几率伪造令牌
        //        $token = Request::instance()->token('token', 'md5');
        cache('session', $wxResult['session_key'], 7200);
        $openid = $wxResult['openid'];

        $user = User::getByOpenID($openid);
        if (!$user)
            // 借助微信的openid作为用户标识
            // 但在系统中的相关查询还是使用自己的uid
        {
            $uid = $this->newUser($openid);
        }
        else {
            $uid = $user->id;
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    // 生成令牌
    public static function generateToken()
    {
        $randChar = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('secure.token_salt');
        return md5($randChar . $timestamp . $tokenSalt);
    }

    // 写入缓存
    private function saveToCache($wxResult)
    {
        $key = self::generateToken();
        $value = json_encode($wxResult);
        $expire_in = config('setting.token_expire_in');
        $result = cache($key, $value, $expire_in);
        if (!$result){
            $Token = new TokenException();
            $Token->msg = '服务器缓存异常';
            $Token->errorCode = "10005";
            throw $Token;
        }
        return $key;
    }

    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    private function processLoginError($res){
        $WeChatException = new WeChatException();
        $WeChatException->msg =$res['errmsg'];
        $WeChatException->errorCode = $res['errcode'];
        throw $WeChatException;
    }

    // 创建新用户
    private function newUser($openid)
    {
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        $user = User::create(
            [
                'openid' => $openid,
                'nickname'=>$this->setUserName(),
                'header_img'=>'https://youguan-1257044613.cos.ap-guangzhou.myqcloud.com/user_img.gif'
            ]);
        return $user->id;
    }

    private function setUserName(){
        $strs="QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789";
        $str = substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),4);
        $strNumber = time();
        return $str.'_'.$strNumber;
    }




}
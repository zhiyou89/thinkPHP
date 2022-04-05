<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */

namespace app\api\server;
use think\Db;
use think\Loader;
Loader::import('GetWxTelephone.wxBizDataCrypt',EXTEND_PATH,'.php');
class WxPhone
{
    public function wxTelephone(){
        $appid = config('wx.app_id');
        $sessionKey = cache('session');
        $encryptedData = input('encrypted_data');
        $iv = input('iv');
        $pc = new \WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        return $data;

    }

}
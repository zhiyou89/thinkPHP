<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/9/16
 * Time: 20:41
 */

namespace app\api\server;
use think\Db;
class User
{
    /**
     * @param $pid 上级ID
     * @param int $level 代表为几级分销
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTree($pid,$level = 0){
        static $arr=array();
        $data=Db::table('user')->where('pid',$pid)->select();
        foreach ($data as $key => $value) {
            if($value['pid'] == $pid){
                $value['level'] = $level+1;
                $arr[] = $value;
                $this->getTree($value['id'],$value['level']);
            }
        }
        return $arr;
    }
    public function tree($id)
    {
        $find=Db::table('user')->where('id',$id)->find();	//一级
        $pid=$find['id'];
        $select=Db::table('user')->where('pid',$id)->select();	//递归一级
        return  $this->getTree($pid);

    }



}
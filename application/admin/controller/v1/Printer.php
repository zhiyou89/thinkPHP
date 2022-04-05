<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/31
 * Time: 20:18
 */

namespace app\admin\controller\v1;



use app\admin\controller\Base;
use app\admin\model\Product;
use app\admin\model\PrinterM as PrinterModel;
use app\admin\model\Uploads as UploadsModel;

class Printer extends Base
{
    public function printer(){
        return PrinterModel::Printers();
    }
}
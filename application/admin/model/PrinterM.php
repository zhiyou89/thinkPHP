<?php
/**
 * Created by PhpStorm.
 * User: zhiyou
 * Date: 2020/8/30
 * Time: 3:20
 */

namespace app\admin\model;


use think\Loader;
use think\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;


class PrinterM extends BaseModel
{
    public static function Printers(){
        try {
            // Enter the device file for your USB printer here
            $connector = new FilePrintConnector("Port_#0001.Hub_#0001");
            //$connector = new FilePrintConnector("/dev/usb/lp1");
            //$connector = new FilePrintConnector("/dev/usb/lp2");

            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);
            $printer -> text("Hello World!\n");
            $printer -> cut();

            /* Close printer */
            $printer -> close();
        } catch (Exception $e) {
            echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }
//        $Test = new FilePrintConnector('Port_#0001.Hub_#0001');
    }
}
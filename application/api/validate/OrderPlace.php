<?php


namespace app\api\validate;


use app\lib\exception\BaseException;
use app\lib\exception\OrderException;
use app\lib\exception\ParameterException;
use think\Exception;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'data' => 'require|isNotEmpty|checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
    ];

    protected $singMessage = [
        'product_id' =>'product_id必须为正整数',
        'count' =>'count必须为正整数'
    ];

    protected function checkProducts($values, $rule='', $data='', $field='')
    {
        if(!is_array($values['products'])){
            return $field . 'products数据格式不对';
        }
        $products = $values['products'];
        for ($i = 0; $i < count($products); $i++) {
            $this->checkProduct($products[$i+1]);
        }
        return true;
    }

    private function checkProduct($value)
    {

        $validate = new BaseValidate();
        $validate->rule = $this->singleRule;
        $validate->message = $this->singMessage;

        $result = $validate->check($value);

        if(!$result){
            $error = $validate->error;
            $ParameterException = new ParameterException();
            $ParameterException->msg = $error;
            throw $ParameterException;
        }
    }

}
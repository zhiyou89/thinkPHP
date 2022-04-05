<?php


namespace app\api\server;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\Order as OrderModel;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\lib\exception\ProductException;
use think\Db;
use think\Exception;
use app\api\server\Token as TokenService;
use app\api\model\User as UserModel;
use app\api\model\ParcelLog as ParcelLogModel;

/**
 * 订单类
 * 订单做了以下简化：
 * 创建订单时会检测库存量，但并不会预扣除库存量，因为这需要队列支持
 * 未支付的订单再次支付时可能会出现库存不足的情况
 * 所以，项目采用3次检测
 * 1. 创建订单时检测库存
 * 2. 支付前检测库存
 * 3. 支付成功后检测库存
 */
class Order
{
    protected $oProducts;
    protected $products;
    protected $uid;
    protected $delivery;
    protected $address;
    protected $coupon;
    function __construct()
    {
    }

    /**
     * @param int $uid 用户id
     * @param array $oProducts 订单商品列表
     * @return array 订单商品状态
     * @throws Exception
     */
    public function place($uid, $oProducts, $delivery, $address,$coupon)
    {
        $this->coupon = $coupon;
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        $this->delivery = $delivery;
        $this->address = $address;
        $status = $this->getOrderStatus();

        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        $orderSnap = $this->snapOrder();
        $status = self::createOrderByTrans($orderSnap);
        $status['pass'] = true;
        return $status;
    }

    /**
     * @param string $orderNo 订单号
     * @return array 订单商品状态
     * @throws Exception
     */
    public function checkOrderStock($orderID)
    {
        //        if (!$orderNo)
        //        {
        //            throw new Exception('没有找到订单号');
        //        }
        // 一定要从订单商品表中直接查询
        // 不能从商品表中查询订单商品
        // 这将导致被删除的商品无法查询出订单商品来
        $oProducts = OrderProduct::where('order_id', '=', $orderID)
            ->select();
        $this->products = $this->getProductsByOrder($oProducts);
        $this->oProducts = $oProducts;
        $status = $this->getOrderStatus();

        return $status;
    }

    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
//            ->update(['status' => OrderStatusEnum::DELIVERED]);
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order, $jumpPage);
    }

    /**
     * 根据客户端传过来的订单信息，确定订单状态
     * @return array
     * @throws OrderException
     */
    private function getOrderStatus()
    {

        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'pStatusArray' => []
        ];
        foreach ($this->oProducts as $oProduct) {
            $pStatus =
                $this->getProductStatus(
                    $oProduct['product_id'], $oProduct['count'], $this->products);

            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            array_push($status['pStatusArray'], $pStatus);

        }

        return $status;
    }

    /**
     * 获取每一个商品的状态，确定订单的真实和库存量
     * @param $oPID
     * @param $oCount
     * @param $products
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];
        for ($i = 0; $i < count($products); $i++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            // 客户端传递的productid有可能根本不存在
            $OrderException = new OrderException();
            $OrderException->msg = 'id为' . $oPID . '的商品不存在，订单创建失败';
            throw $OrderException;
        } else {
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }

    /**
     * 根据订单查找真实商品
     * @param $oProducts
     * @return mixed
     * @throws \think\exception\DbException
     */

    private function getProductsByOrder($oProducts)
    {

        $oPIDs = [];

        foreach ($oProducts as $item) {
            array_push($oPIDs, $item['product_id']);
        }
        // 为了避免循环查询数据库
        $products = Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url','product_describe','market_price','index_category_id','onsell','price_2','price_3'])
            ->toArray();

        return $products;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)
            ->find();
        if (!$userAddress) {
            $UserException = new UserException();
            $UserException->msg = '用户收货地址不存在，下单失败';
            $UserException->code = 6001;
            throw $UserException;
        }
        return $userAddress->toArray();
    }

    // 创建订单时没有预扣除库存量，简化处理
    // 如果预扣除了库存量需要队列支持，且需要使用锁机制
    private function createOrderByTrans($snap)
    {
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_delivery = json_encode($this->delivery);
            $order->snap_items = json_encode($snap['pStatus']);
            $order->coupon = $this->coupon;
            $order->save();

            $orderID = $order->id;

            $create_time = $order->create_time;

            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        } catch (Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }
    /**
     * 预检测并生成订单快照
     * @return array
     */

    private function snapOrder()
    {
        // status可以单独定义一个类
        $snapProducts = [];
        $snapProducts['products'] = $this->products;
        $snapProducts['delivery'] = $this->delivery;

        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => json_encode($this->address),
            'snapName' => $this->products[0]['product_describe'],
            'snapImg' => $this->products[0]['main_img_url'],
            'snapProducts' => json_encode($snapProducts)
        ];

        for ($i = 0; $i < count($this->products); $i++) {
            $product = $this->products[$i];
            for($j = 0; $j<count($this->oProducts);$j++){
               $oProducts =  $this->oProducts[$j];
                if($product['id'] == $oProducts['product_id']){
                    $oProduct = $this->oProducts[$j];
                    $pStatus = $this->snapProduct($product, $oProduct['count'],$oProduct['properties'],$oProduct['index_category_id']);

                    $snap['orderPrice'] += $pStatus['totalPrice'];
                    $snap['totalCount'] += $pStatus['count'];

                    array_push($snap['pStatus'], $pStatus);
                }
            }
        }
        return $snap;
    }



    // 单个商品库存检测
    private function snapProduct($product, $oCount,$properties,$navID)
    {
        $pStatus = [
            'id' => null,
            'name' => null,
            'main_img_url'=>null,
            'count' => $oCount,
            'totalPrice' => 0,
            'price' => 0
        ];

        $pStatus['status'] = 0;
        $pStatus['counts'] = $oCount;
        $productModel =new Product();
        $product = $productModel->getPoductTruePrice($navID,$product);
        // 以服务器价格为准，生成订单
        $pStatus['totalPrice'] = $oCount * $product['price'];
        $pStatus['name'] = $product['product_describe'];
        $pStatus['id'] = $product['id'];
        $pStatus['main_img_url'] =$product['main_img_url'];
        $pStatus['price'] = $product['price'];
        $pStatus['index_category_id'] = $navID;
        $pStatus['properties'] = $properties;
        return $pStatus;
    }


    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * 1找出上两级
     * 2给上两级分佣
     * 3写入记录
     */
    public static function fenYong($order){
        $upId = 0;
        $upUpId = 0;
        $uid = TokenService::getCurrentUid();
        $myInfo =UserModel::get($uid);

        if($myInfo->pid){
            $upId = $myInfo->pid;
            $upInfo = UserModel::get($myInfo->pid);
            if($upInfo->pid){
                $upUpId = $upInfo->pid;
            }
        }
        $b_friends_p = config('setting.b_friends_p');
        $a_friends_p = config('setting.a_friends_p');
            if($upId){
                UserModel::where('id',$upId)->setInc('performance',$order->total_price);
                UserModel::where('id',$upId)->setInc('z_money',$order->total_price*$b_friends_p);
                UserModel::where('id',$upId)->setInc('money',$order->total_price*$b_friends_p);
                ParcelLogModel::create([
                    'order_id'=>$order->id,
                    'price'=>$order->total_price,
                    'user_id'=>$uid,
                    'get_money'=>$order->total_price*$b_friends_p,
                    'create_time'=>time(),
                    'up_id'=>$upId,
                    'level'=>1,
                ]);
            }
            if($upUpId){
                UserModel::where('id',$upUpId)->setInc('performance',$order->total_price);
                UserModel::where('id',$upUpId)->setInc('z_money',$order->total_price*$a_friends_p);
                UserModel::where('id',$upUpId)->setInc('money',$order->total_price*$a_friends_p);
                ParcelLogModel::create([
                    'order_id'=>$order->id,
                    'price'=>$order->total_price,
                    'user_id'=>$uid,
                    'get_money'=>$order->total_price*$a_friends_p,
                    'create_time'=>time(),
                    'up_id'=>$upUpId,
                    'level'=>2,
                ]);
            }
        return true;

    }
}
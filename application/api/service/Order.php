<?php

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\OrderDetail as  OrderDetailModel;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;

class Order
{
    protected $uid;

    public function place($uid,$oMsg){
        $this->uid = $uid;
        //创建订单
        $order = $this->createOrder($oMsg);

        return $order;
    }

    private function createOrder($oMsg){
        Db::startTrans();
        try{

            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->status = 0;
            $order->get_phone = $oMsg['get_phone'];
            $order->unlock_time = strtotime($oMsg['date']);
            //这里还要算price
            $order->price = ceil(($order->unlock_time-time())/(60*60*24*365))*2;
            $order->save();
            
            $orderID = $order->id;
            $create_time = $order->create_time;
            $order_price = $order->price;

            $orderDetail = new OrderDetailModel();
            $orderDetail->order_id = $orderID;
            $orderDetail->text = $oMsg['text'];
            $orderDetail->imgs = $oMsg['imgs'];
            //现在还没有加入录音所以注释
            // $orderDetail->music = $oMsg['music'];
            $orderDetail->save();
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time,
                'order_price' => $order_price
            ];
        }catch(\Exception $ex){
            Db::rollBack();
            throw $ex;
        }

    }

    //生成订单编号
    public static function makeOrderNo(){
        $yCode = array('A','B','C','D','E','F','G','H','I','J','K','M');

        $orderSn = $yCode[intval(date('Y'))-2017].strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $orderSn;
    }


}
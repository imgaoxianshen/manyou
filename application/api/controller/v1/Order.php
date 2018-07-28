<?php

namespace app\api\controller\v1;

use app\api\service\Token as TokenService;
use app\lib\exception\TokenException;
use app\lib\exception\SmsException;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\api\service\Sms;
use app\lib\exception\SuccessMessage;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'placeOrder']
    ];
    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $oMsg['text'] = input('post.text');
        $oMsg['imgs'] = input('post.imgs');
        $oMsg['get_phone'] = input('post.get_phone');
        $oMsg['date'] = input('post.date');
        // $oMsg['music'] = input('post.music');
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid,$oMsg);
        return new SuccessMessage(['data'=>$status]);;
    }
    //信列表
    public function orderList(){
        $uid = TokenService::getCurrentUid();
        $order = new OrderModel();
        $order_list = $order::orderList($uid);
        return new SuccessMessage(['data'=>$order_list]);
    }
    //信的详情
    public function getOrderOne($order_id){
        $uid = TokenService::getCurrentUid();
        $order = new OrderModel();
        $order_detail = $order::getOrderOne($uid,$order_id);
        return new SuccessMessage(['data'=>$order_detail]);
    }

    //收到的信
    public function orderGet(){
        $uid = TokenService::getCurrentUid();
        $order = new OrderModel();
        $order_list = $order::orderGet($uid);
        foreach($order_list as &$l){
            $l['type'] = 'get';
        }
        return new SuccessMessage(['data'=>$order_list]);
    }
    //发出的信
    public function orderSend(){
        $uid = TokenService::getCurrentUid();
        $order = new OrderModel();
        $order_list = $order::orderSend($uid);
        foreach($order_list as &$l){
            $l['type'] = 'send';
        }

        return new SuccessMessage(['data'=>$order_list]);
    }

    //查看信件
    public function watchOrder($order_id){
        $uid = TokenService::getCurrentUid();
        //order需要返回发送的人的手机号
        $order = new OrderModel();
        $res = $order::watchOrder($uid,$order_id);
        
        if(!$res){
            //不处理直接返回
            return new SuccessMessage();
        }
        //处理更改状态以及发送sms
        $order::where('id','=',$order_id)->update(['status'=>2]);
        $msg = Sms::sendSms($res,1111);
        
        if($msg['code']!="OK"){
            throw new SmsException();
        }
        return new SuccessMessage();
    }
}
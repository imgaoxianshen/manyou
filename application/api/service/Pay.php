<?php
namespace app\api\service;

use app\api\model\Order as OrderModel;
use think\Exception;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\lib\enum\OrderStatusEnum;
use wxpay\WxPayApi;
use wxpay\WxPayUnifiedOrder;
use wxpay\WxPayJsApiPay;
use think\Log;


class Pay{
    private $orderID;
    private $orderNO;
    private $orderPrice;

    function __construct($orderID){
        if(!$orderID){
            throw new Exception('订单号不允许为空');
        }
        $this->orderID = $orderID;
    }

    public function pay(){
        $this->checkOrderValid();
        return $this->makeWxPreOrder();
    }

    private function makeWxPreOrder(){
     
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData = new WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->setTotal_fee($this->orderPrice*100);
        $wxOrderData->setBody('time');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
     
    }

    private function getPaySignature($wxOrderData){
        $wxOrder = WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code']!="SUCCESS" || $wxOrder['result_code'] !="SUCCESS"){
            dump($wxOrder);
            // Log::record($wxOrder,'errpr');
            // Log::record("获取于支付订单失败",'error');
        }
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function sign($wxOrder){
        $jsApiPayData = new WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;

        unset($rawValues['appId']);

        return $rawValues;
    }

    private function recordPreOrder($wxOrder){
        OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    private function checkOrderValid(){
        $order = OrderModel::where('id' ,'=',$this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }
        if(!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg'=>'订单与用户不匹配',
                'errorCode'=>10003
            ]);
        }
        if($order->status != OrderStatusEnum::UNPAIED){
            throw new OrderException([
                'msg'=>'订单已支付过',
                'errorCode'=>80003,
                'code'=>400
            ]); 
        }

        $this->orderNO = $order->order_no;
        $this->orderPrice = $order->price;
        return true;
    }
}
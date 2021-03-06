<?php

namespace app\api\Service;

use wxpay\WxPayNotify;
use app\api\model\Order as OrderModel;
use app\lib\enum\OrderStatusEnum;
use app\api\service\Sms;
use think\Db;
use app\lib\enum\SmsTemplate;

class WxNotify extends WxPayNotify{

    public function  NotifyProcess($data,&$msg){
        if($data['result_code'] == "SUCCESS"){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try{
                $order = OrderModel::where("order_no","=",$orderNo)->lock(true)->find();

                if($order->status == OrderStatusEnum::UNPAIED){
                    OrderModel::where("order_no","=",$orderNo)->update(["status"=>OrderStatusEnum::PAYID]);
                }
                Db::commit();
            
                //这里还有发送sms
                $res = Sms::sendSms($order['get_phone'],$order['name'],SmsTemplate::START_SEND);
                return true;

            }catch(\Exception $e){
                Db::rollBack();
                return false;
            }
        }else{
            return true;
        }
    }

}
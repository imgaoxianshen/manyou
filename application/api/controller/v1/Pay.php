<?php

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify as WxNotifySercice;
use app\lib\exception\SuccessMessage;
use app\api\model\Order;
use app\api\service\Token as TokenService;
use app\api\service\Sms;
use app\lib\enum\SmsTemplate;

class  Pay extends BaseController{
    protected $beforeActionList = [
        'checkExclusiveScope' =>['only'=>'getPerOrder']
    ];

    public function getPerOrder($id = '',$checked)
    {
        (new IDMustBePositiveInt())->goCheck();
        $payService = new PayService($id);
        if($checked ==1){
            return new SuccessMessage(['data'=>$payService->pay()]);
        }else{
            $uid = TokenService::getCurrentUid();
            $order = Order::payOrder($id,$uid);
            // $res = Sms::sendSms($order['get_phone'],$order['name'],SmsTemplate::START_SEND);
            $res = Sms::sendSms('15669762297','asdasd',SmsTemplate::START_SEND);
            return new SuccessMessage($res);
        }
        
    }

    public function receiveNotify(){
        $notify = new WxNotifySercice();
        $notify->Handle();
    }
}
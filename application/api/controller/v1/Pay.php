<?php

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify as WxNotifySercice;
use app\lib\exception\SuccessMessage;
use app\api\model\Order;

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
            Order::payOrder($id,$uid);
            return new SuccessMessage();
        }
        
    }

    public function receiveNotify(){
        $notify = new WxNotifySercice();
        $notify->Handle();
    }
}
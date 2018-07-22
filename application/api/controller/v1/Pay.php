<?php

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify as WxNotifySercice;
use app\lib\exception\SuccessMessage;

class  Pay extends BaseController{
    protected $beforeActionList = [
        'checkExclusiveScope' =>['only'=>'getPerOrder']
    ];

    public function getPerOrder($id = '')
    {
        (new IDMustBePositiveInt())->goCheck();
        $payService = new PayService($id);
        return new SuccessMessage(['data'=>$payService->pay()]);
    }

    public function receiveNotify(){
        $notify = new WxNotifySercice();
        $notify->Handle();
    }
}
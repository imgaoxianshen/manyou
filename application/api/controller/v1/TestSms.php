<?php

namespace app\api\controller\v1;

use app\api\service\Sms;
use app\api\service\Token as TokenService;
use app\lib\exception\UnBindPhoneException;
use app\api\validate\Mobile;
use app\lib\exception\SuccessMessage;

class TestSms 
{
    public function sms(){
        $res = Sms::sendSms(15669762297,12345);
        return $res;
    }

    public function sendCode($mobile){
        (new Mobile())->goCheck();
        //对mobile校验
        $code = rand(100000,999999);
        Sms::sendSms($mobile,$code);
        
        return new SuccessMessage(['data'=>$code]);
    }

}
<?php
namespace app\lib\enum;

class SmsTemplate
{   
    //新件被查看
    const WATCHED = 'SMS_140735871';

    //即将送达
    const READY_TO = 'SMS_140735870';

    //绑定手机号
    const BIND_PHONE = 'SMS_141615835';

    //发送了一封信件
    const START_SEND = 'SMS_141580980';
}
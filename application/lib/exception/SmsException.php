<?php

namespace app\lib\exception;

class SmsException extends BaseException
{
    public $code = 444;
    public $msg = '短信发送失败';
    public $errorCode = 10000;
}
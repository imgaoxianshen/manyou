<?php

namespace app\lib\exception;


class PayException extends BaseException
{
    public $code = 400;
    public $msg = '支付失败';
    public $errorCode = 10000;
}
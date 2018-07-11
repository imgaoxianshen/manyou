<?php

namespace app\lib\exception;

class UnBindPhoneException extends BaseException
{
    public $code = 404;

    public $msg = '手机号码未绑定';

    public $errorCode = 40000;
}
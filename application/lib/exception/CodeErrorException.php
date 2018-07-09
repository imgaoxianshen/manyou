<?php

namespace app\lib\exception;

class CodeErrorException extends BaseException
{
    public $code = 300;

    public $msg = '验证码错误，请重试';

    public $errorCode = 40002;
}
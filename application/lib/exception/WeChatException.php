<?php

namespace app\lib\exception;

class WeChatException extends BaseException
{
    public $code = 404;
    public $msg = '微信服务器返回异常，请重试';
    public $errorCode = 30000;
}
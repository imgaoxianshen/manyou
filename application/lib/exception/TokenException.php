<?php

namespace app\lib\exception;

class TokenException extends BaseException
{
    public $code =401;
    public $msg = 'Token已过期或者无效Token';
    public $errorCode = 10001;
}
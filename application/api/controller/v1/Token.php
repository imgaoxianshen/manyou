<?php
namespace app\api\controller\v1;

use app\api\validate\TokenGet;
use app\api\service\UserToken;
use app\lib\exception\SuccessMessage;

class Token
{
    public function getToken($code=''){
        (new TokenGet())->goCheck();
        $userToken = new UserToken($code);
        $token = $userToken->get();

        return new SuccessMessage(['data'=>$token]);
    }
}
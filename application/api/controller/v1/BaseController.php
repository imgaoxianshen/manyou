<?php

namespace app\api\controller\v1;

use think\Controller;
use app\api\service\Token as TokenService;
class BaseController extends controller
{
    protected function checkPrimaryScope(){
        TokenService::needPrimaryScope();
    }
    
    protected function checkExclusiveScope(){
        TokenService::needExclusiveScope();
    }
}
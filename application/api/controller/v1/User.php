<?php
namespace app\api\controller\v1;

use app\api\validate\TokenGet;
use app\api\service\UserToken;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use think\facade\Cache;
use app\lib\exception\CodeErrorException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UnBindPhoneException;

class User
{
    public function bindMobile($mobile){
        //绑定手机号
        $uid = TokenService::getCurrentUid();
        $user = new UserModel();
        $user::bindMobile($uid,$mobile);
        return new SuccessMessage();
    }

    public function getMobile(){
        $uid = TokenService::getCurrentUid();
        $user = new UserModel();
        $mobile = $user->field('mobile')->where('id','=',$uid)->find();
        if(empty($mobile['mobile'])){
            throw new UnBindPhoneException();
        }
        return new SuccessMessage(['data'=>$mobile['mobile']]);
    }
}
<?php
namespace app\api\controller\v1;

use app\api\validate\TokenGet;
use app\api\validate\Mobile;
use app\api\service\UserToken;
use app\api\service\Sms;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use think\facade\Cache;
use app\lib\exception\CodeErrorException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UnBindPhoneException;
use app\lib\enum\SmsTemplate;


class User
{
    public function bindMobile($mobile,$tuiguangMobile){
        $incMoney = 2;
        //绑定手机号
        $uid = TokenService::getCurrentUid();
        $user = new UserModel();
        //先查推荐人手机号是否存在
        //存在的话继续
        if(!empty($tuiguangMobile)){
            $tuiguangUser = $user->where('mobile','=',$tuiguangMobile)->find();
            if(empty($tuiguangUser)){
                throw new UnBindPhoneException(['msg'=>'推荐人手机号不存在，请重试']);
            }
        }
        
        $user::bindMobile($uid,$mobile,$tuiguangMobile);

        if(!empty($tuiguangMobile)){
            //相互时间钱币+2
            $user->where('id','=',$uid)->setInc('money',$incMoney);
            $user->where('mobile','=',$tuiguangMobile)->setInc('money',$incMoney);
        }
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

    public function sendCode($mobile){
        (new Mobile())->goCheck();
        //对mobile校验
        $code = rand(1000,9999);
        Sms::sendSms($mobile,$code,SmsTemplate::BIND_PHONE);
        
        return new SuccessMessage(['data'=>$code]);
    }
}
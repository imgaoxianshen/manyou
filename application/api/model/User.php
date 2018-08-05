<?php

namespace app\api\model;

class User extends BaseModel
{
    protected $hidden = ['create_time','delete_time','update_time','openid'];

    public function address()
    {
        return $this->hasOne('UserAddress','user_id','id');     
    }

    public static function getByOpenID($openid){
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }

    public static function bindMobile($uid,$mobile,$tuiguangMobile){
        return self::where('id','=',$uid)->update(['mobile'=>$mobile,'tuiguangMobile'=>$tuiguangMobile]);
    }
}
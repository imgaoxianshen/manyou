<?php

namespace app\api\model;

use app\lib\enum\OrderStatusEnum;
use app\api\model\User;
use app\lib\exception\UnBindPhoneException;

class Order extends BaseModel
{
    protected $hidden = ['user_id','delete_time','update_time'];
    protected $autoWriteTimestamp = true;

    public function orderDetail(){
        return $this->hasOne('OrderDetail');
    }

    public function User(){
        return $this->belongsTo('User');
    }

    //首页的所有信的列表（收到的和寄出去的）
    public static function orderList($uid){
        //还要查他收到的
        $user = User::field("mobile")->where("id","=",$uid)->find();
        if(empty($user)){
            throw new UnBindPhoneException();
        }
        $orderList = self::with('User')->where(function($query) use ($user,$uid){
            $query->where('user_id','=',$uid)->whereOr('get_phone','=',$user['mobile']);
        })->where('status','<>',OrderStatusEnum::UNPAIED)->order('create_time','desc')->select();

        foreach($orderList as &$order){
            if($order['get_phone'] == $user['mobile']){
                $order['type'] = 'get';
            }else{
                $order['type'] = 'send';
            }
        }
        
        return $orderList;
    }

    public static function getOrderOne($uid,$order_id){
        $user = User::field("mobile")->where("id","=",$uid)->find();
        if(empty($user)){
            throw new UnBindPhoneException();
        }
        $order = self::with('orderDetail')->where('status','<>',OrderStatusEnum::UNPAIED)
        ->where(function($query) use ($user,$uid){
            $query->where('user_id','=',$uid)->whereOr('get_phone','=',$user['mobile']);
        })->where('id','=',$order_id)->find();

        if($order['get_mobile'] == $user['mobile']){
            $order['type'] == "get";
        }else{
            $order['type'] == "send";           
        }
        
        return $order;
    }

    public static function orderGet($uid){
        //先查这个人的手机号
        $user = User::field("mobile")->where("id","=",$uid)->find();
        if(empty($user)){
            throw new UnBindPhoneException();
        }
        //这里用phone查询订单列表
        return self::where('status','<>',OrderStatusEnum::UNPAIED)->where('get_phone','=',$user['mobile'])->select(); 
    }

    //这个人发送的信
    public static function orderSend($uid){
        return self::where('status','<>',OrderStatusEnum::UNPAIED)->where('user_id','=',$uid)->select();
    }

    public static function watchOrder($uid,$order_id){
        //先判断是否已读
        $order = self::with('User')->where('status','<>',OrderStatusEnum::UNPAIED)->where('id','=',$order_id)->find();
        
        if($order['status'] == 2){
            return false;
        }
        //判断这个order_id的收件人是不是这个uid
        $user = User::field("mobile")->where("id","=",$uid)->find();

        if($order['get_phone'] != $user['mobile']){
            return false;
        }
        
        //返回发件人的mobile
        return $order['user']['mobile'];

    }
}
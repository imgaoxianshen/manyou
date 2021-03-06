<?php

namespace app\api\model;

use app\lib\enum\OrderStatusEnum;
use app\api\model\User;
use app\lib\exception\UnBindPhoneException;
use app\lib\exception\PayException;

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
            if($order['status'] == 1){
                $order['left_time'] = ($order['unlock_time'] <= time()) ? -1 : floor(($order['unlock_time']-time())/(60*60*24));
            }
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

        if($order['get_phone'] == $user['mobile']){
            $order['type'] = "get";
        }else{
            $order['type'] = "send";           
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
        $orderList = self::with('User')->where('status','<>',OrderStatusEnum::UNPAIED)->where('get_phone','=',$user['mobile'])->order('create_time','desc')->select(); 
        foreach($orderList as $order){
            if($order['status'] == 1){
                $order['left_time'] = ($order['unlock_time'] <= time()) ? -1 : floor(($order['unlock_time']-time())/(60*60*24));
            }
        }
        return $orderList;
    }

    //这个人发送的信
    public static function orderSend($uid){
        $user = User::field("mobile")->where("id","=",$uid)->find();
        //需要屏蔽发送给自己的信
        $orderList = self::where('status','<>',OrderStatusEnum::UNPAIED)->where('user_id','=',$uid)->order('create_time','desc')->select(); 
        foreach($orderList as $k => $order){
            if($order['status'] == 1){
                $order['left_time'] = ($order['unlock_time'] <= time()) ? -1 : floor(($order['unlock_time']-time())/(60*60*24));
            }
            if($order['get_phone'] == $user['mobile']){
                unset($orderList[$k]);
            }
        }
        return $orderList;
    }

    public static function watchOrder($uid,$order_id){
        //先判断是否已读
        $order = self::with('User')->where('status','<>',OrderStatusEnum::UNPAIED)->where('id','=',$order_id)->find();
        
        if($order['status'] == 2){
            return false;
        }
        if($order['unlock_time']>time()){
            return false;
        }
        //判断这个order_id的收件人是不是这个uid
        $user = User::field("mobile")->where("id","=",$uid)->find();

        if($order['get_phone'] != $user['mobile']){
            return false;
        }
        
        //返回发件人的mobile
        return ['mobile'=>$order['user']['mobile'],'get_phone'=>$order['get_phone']];

    }

    public static function unlockList(){
        //今日0：00的到明天0:00
        return self::where('status','=',OrderStatusEnum::PAYID)->where('unlock_time','>=',strtotime(date("Y-m-d"),time()))
        ->where('unlock_time','<',strtotime(date('Y-m-d',time()+24*60*60)))->select();
    }

    public static function payOrder($id,$uid){
        $order = self::with('User')->where('id','=',$id)->where('status','=',OrderStatusEnum::UNPAIED)->find();
        if(empty($order)){
            throw new PayException([
                'msg' => '此订单已经被支付或者该订单不存在'
            ]);
        }
        if($uid != $order['user']['id']){
            throw new PayException([
                'msg' => '用户信息错误'
            ]);
        }
        if($order['user']['money']>$order['price']){
            try{
                self::startTrans();
                self::where('id','=',$id)->update(['status' => OrderStatusEnum::PAYID]);
                User::where('id','=',$uid)->setDec('money', $order['price']);
                self::commit();
                return $order;
            }catch(\Exception $e){
                self::rollback();
                throw new PayException([
                    'msg' => '支付失败，请重试'
                ]);
            }
        }else{
            throw new PayException([
                'msg' => '支付失败，时间币不足'
            ]);
        }
        
    }
}
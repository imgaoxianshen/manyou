<?php

namespace app\api\service;

use think\facade\Request;
use think\facade\Cache;
use app\lib\exception\TokenException;
use think\Exception;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;

class Token 
{
    public static function generateToken(){
        $randChars = getRandChars(32);
        $timestamp = time();
        //加盐
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }

    public static function getCurrentTokenVar($key){
        $token = Request::header('token');

        $vars = Cache::get($token);

        if(!$vars){
            throw new TokenException();

        }else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }

            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取的Token变量不存在');
            }
        }
    }

    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    //用户和管理员都可以访问
    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope>=ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException;
            }
    
        }else{
            throw new TokenException;
        }
    }

    //只有用户可以访问
    public static function needExclusiveScope(){
        $scope = TokenService::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException;
            }
    
        }else{
            throw new TokenException;
        }
    }

    public static function isValidOperate($checkedUID){
        if(!$checkedUID){
            throw new Exception('检查UID是否被传入');
        }

        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID){
            return true;
        }
        return false;
    }


}
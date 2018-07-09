<?php

namespace app\api\validate;

use think\Validate;
use think\facade\Request;
use think\Exception;
use app\lib\exception\ParameterException;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        //1.获取传入参数
        //2.校验
        // $request = Request::instance();
        $params = Request::param();

        $result = $this->batch()->check($params);
        if(!$result){
            $e =  new ParameterException([
                'msg'=>$this->getError()
            ]);
            throw $e;
        }else{
            return true;
        }
    }

    protected function isPositiveInterger($value,$rule=[],$data=[],$field='')
    {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0 ){
            return true;
        }else{
            return false;
        }
    }

    
    public function isNotEmpty($value,$rule = [],$data = [],$field=''){
        if(empty($value)){
            return false;
        }else {
            return true;
        }
    }

    public function isMobile($value){
        $rule = '^1(3|4|5|6|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule,$value);
        if($result){
            return true;
        }else{
            return false;
        }
    }

    public function getDataByRule($arrays){
        if( array_key_exists('user_id',$arrays) |
            array_key_exists('uid',$arrays) )
        {
            throw new ParameterException([
                'msg'=>'参数中包含有非法的参数名user_id或uid'
            ]);
            
        }

        $newArray = [];

        foreach($this->rule as $key=>$value){
            $newArray[$key] = $arrays[$key];
        }

        return $newArray;

    }
}
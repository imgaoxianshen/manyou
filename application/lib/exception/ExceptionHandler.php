<?php

namespace app\lib\exception;

// use think\Exception;
use think\exception\Handle;
use think\Request;
use think\facade\Log;

class ExceptionHandler  extends  Handle
{
    private $code;
    private $msg;
    private $errorCode;
    
    //返回当前请求路径
    public function render(\Exception $e){
       if($e instanceof BaseException){
        //如果是自定义的异常
        $this->code = $e->code;
        $this->msg = $e->msg;
        $this->errorCode = $e->errorCode;
       }else{
        $switch = true;
        if($switch){
            return parent::render($e);
        }else{
            $this->code = 500;
            $this->msg = '服务器内部错误';
            $this->errorCode = 999;
            $this->recordErrorLog($e);
        }
        
       }
       
       $request = new Request();
       $result = [
            'msg'=>$this->msg,
            'error_code'=>$this->errorCode,
            'request_url'=>$request->url()
       ];

       return json($result,$this->code);
    }


    private function recordErrorLog(\Exception $e){
        Log::init([
            'type'=>'File',
            'path'=>__DIR__."/../log/",
            'level'=>['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}
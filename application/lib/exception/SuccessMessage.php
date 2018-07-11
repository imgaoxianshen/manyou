<?php
namespace app\lib\exception;

class SuccessMessage
{
    public $code = 200;
    public $msg = 'ok';
    public $errorCode = 0;
    public $data =[];

    public function __construct($params = []){
        if(!is_array($params)){ 
            return;
        }
        if(array_key_exists('data',$params)){
            $this->data = $params['data'];
        }
    }
}
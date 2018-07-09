<?php

namespace app\api\validate;

class Mobile extends BaseValidate
{
    protected $rule = [
        'mobile'=>'require|mobile'
    ];

    protected $message = [
        'id'=>'请查看手机格式是否正确'
    ];
}
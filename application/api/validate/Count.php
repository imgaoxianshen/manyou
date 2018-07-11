<?php

namespace app\api\validate;

class Count extends BaseValidate
{
    protected $rule = [
        'count'=>'isPositiveInterger|between:1,15'
    ];
} 
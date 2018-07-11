<?php

namespace app\api\model;

class OrderDetail extends BaseModel
{
    protected $hidden = ['order_id','id'];
    protected $autoWriteTimeStamp = true;
}
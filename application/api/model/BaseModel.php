<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    
    protected function perfixImgUrl($value,$data){
        if($data['from'] == 1){
            return config('setting.img_prefix').$value;
        }
        
        return $value;    
    }
}

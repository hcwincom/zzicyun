<?php

namespace app\money\model;

use think\Db;
use think\Model;

class CreditApplyModel extends Model
{
    /**
     *授信申请
     */
    public function apply($data){
        $id=$this->insertGetId($data);
        return $id;
    }
 
}

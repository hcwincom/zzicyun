<?php

namespace app\user\model;

use think\Db;
use think\Model;

class UserAddressModel extends Model
{
    public function get_all($uid,$type=1){
        $where=[
            'uid'=>$uid,
            'type'=>$type,
        ];
        $list=$this->where($where)->order('is_first asc')->column('');
        return $list;
    }
 
}

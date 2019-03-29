<?php

namespace app\user\model;

use think\Db;
use think\Model;

class UserAddressModel extends Model
{
    public function get_all($uid,$type=0){
        $where=[
            'uid'=>$uid, 
        ];
        if($type>0){
            $where['type']=$type;
        }
        $list=$this->where($where)->order('is_first asc')->column('');
        return $list;
    }
    public function get_one($uid,$id=0){
        $where=[
            'uid'=>$uid, 
        ];
        if(!empty($id)){
            $where['id']=$id;
        }
        $info=$this->where($where)->order('is_first asc')->find(); 
        return $info->getData();
    }
 
}

<?php

namespace app\money\model;

use think\Db;
use think\Model;

class BankModel extends Model
{
    /**
     *银联链接展示
     */
    public function get_shows(){
        $where=['status'=>2,'is_show'=>1];
       
        $list=$this->where($where)->column('id,name,pic');
        
        return $list;
    }
    /**
     *列表
     */
    public function get_names(){
        $where=['status'=>2];
        
        $list=$this->where($where)->column('id,name');
        
        return $list;
    }
     
}

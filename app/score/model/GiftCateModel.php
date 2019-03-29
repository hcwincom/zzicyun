<?php
 
namespace app\score\model;

use think\Model; 

class GiftCateModel extends Model
{
     /**
      * 获取所有礼品分类
      */
    public function get_names(){
        $where=[ 
            'status'=>2,
             
        ];
        $list=$this->where($where)->order('sort asc')->column('id,name');
        return $list;
    }
    
    
}

<?php
 
namespace app\score\model;

use think\Model; 

class ScoreRuleModel extends Model
{
     /**
      * 获取所有积分规则
      */
    public function get_list(){
        $where=[ 
            'status'=>2,
             
        ];
        $list=$this->where($where)->order('sort asc')->column('');
        return $list;
    }
    /**
     * 获取所有积分规则
     */
    public function get_names(){
        $where=[
            'status'=>2,
            
        ];
        $list=$this->where($where)->order('sort asc')->column('id,name');
        return $list;
    }
    
    
}

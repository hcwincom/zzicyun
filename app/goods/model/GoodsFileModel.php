<?php
 
namespace app\goods\model;

use think\Model; 
class GoodsFileModel extends Model
{
    public function get_limit($limit=8){
        $res=$this->order('sort asc')->column('');
        return $res;
    }
    public function get_all_by_id($pid){
        $res=$this->where('pid',$pid)->column('*','type');
        return $res;
    }
     
}

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
    public function get_all_by_ids($goods_ids){
        $res=$this->where('pid','in',$goods_ids)->column('*');
        $list=[];
        foreach($res as $k=>$v){
            $list[$v['pid']][$v['type']]=$v;
        }
        return $list;
    }
     
}

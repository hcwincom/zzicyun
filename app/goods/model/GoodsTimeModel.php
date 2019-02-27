<?php
 
namespace app\goods\model;

use think\Model;
use think\Db;
class GoodsTimeModel extends Model
{
    
    
    /**
     * 获取取货时间
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_list($lan=1,$lan1=1,$where=['p.status'=>2],$limit=0){
        
        $field='p.id,val.val as name';
        $order='p.sort asc';
        if(empty($limit)){
            $list=$this
            ->alias('p')
            ->join('cmf_goods_time_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->column($field);
        }else{
            $list=$this
            ->alias('p')
            ->join('cmf_goods_time_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->column($field);
        }
        
        if(empty($list)){
            if(empty($limit)){
                $list=$this
                ->alias('p')
                ->join('cmf_goods_time_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->column($field);
            }else{
                $list=$this
                ->alias('p')
                ->join('cmf_goods_time_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->column($field);
            } 
        }
       
        return $list;
    }
    
}

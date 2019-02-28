<?php
 
namespace app\shop\model;

use think\Model;
use think\Db;
class ShopModel extends Model
{
    /**
     * 获取供应商
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_limit($lan=1,$lan1=1,$where=['p.status'=>2],$limit=0){
     
        $field='p.id,p.logo,p.type,val.name as name,val.char,val.brands,val.pros';
        $order='p.sort asc';
        if(empty($limit)){
            $list=$this
            ->alias('p')
            ->join('cmf_shop_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->column($field);
        }else{
            $list=$this
            ->alias('p')
            ->join('cmf_shop_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->column($field);
        }
       
        if(empty($list)){
            if(empty($limit)){
                $list=$this
                ->alias('p')
                ->join('cmf_shop_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->column($field);
            }else{
                $list=$this
                ->alias('p')
                ->join('cmf_shop_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->column($field);
            }
           
        }
        
        return $list;
    }
     
}

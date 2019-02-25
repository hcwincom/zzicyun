<?php
 
namespace app\goods\model;

use think\Model;
use think\Db;
class GoodsBrandCateModel extends Model
{
     
    /**
     * 获取品牌类型
     * @param number $lan
     * @param number $lan1 
     * @return array
     */
    public function get_all($lan=1,$lan1=1,$where=['p.status'=>2]){
        
        
        //品牌分类  
        $field='p.id,val.val as name';
        $order='p.sort asc';
        $brand_cates=$this
        ->alias('p')
        ->join('cmf_goods_brand_cate_val val','val.pid=p.id and val.lid='.$lan) 
        ->where($where)
        ->order($order)
        ->column($field);
        if(empty($brand_cates)){
            $brand_cates=$this
            ->alias('p')
            ->join('cmf_goods_brand_cate_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->order($order)
            ->column($field);
        }
        return $brand_cates;
    }
    
}

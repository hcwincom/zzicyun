<?php
 
namespace app\goods\model;

use think\Model;
use think\Db;
class GoodsBrandModel extends Model
{
    /**
     * 管理后台选择用
     * @return array
     */
    public function get_all(){
       $res=$this->where('status',2)->order('sort asc')->column('id,name');
        
        return $res;
    }
    /**
     * 前台获取
     * @param number $limit
     * @return array
     */
    public function get_limit($limit=0){
         $res=$this->where('status',2)->order('sort asc')->limit($limit)->column('id,name,pic');
       
        return $res;
    }
    /**
     * 获取品牌
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_list($lan=1,$lan1=1,$where=['p.status'=>2],$limit=0){
        
        $field='p.id,p.pic,p.cid,val.name as name,val.char';
        $order='p.sort asc';
        if(empty($limit)){
            $list=$this
            ->alias('p')
            ->join('cmf_goods_brand_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->column($field);
        }else{
            $list=$this
            ->alias('p')
            ->join('cmf_goods_brand_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->column($field);
        }
        
        if(empty($list)){
            if(empty($limit)){
                $list=$this
                ->alias('p')
                ->join('cmf_goods_brand_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->column($field);
            }else{
                $list=$this
                ->alias('p')
                ->join('cmf_goods_brand_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->column($field);
            } 
        }
       
        return $list;
    }
    
    /**
     * 获取品牌详情
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_one($lan=1,$lan1=1,$id){
        $where=['p.id'=>$id];
        $field='p.id,p.pic,p.goods_num,val.name as name,val.char,val.dsc';
        
        
        $info=$this
        ->alias('p')
        ->field($field)
        ->join('cmf_goods_brand_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where)
        ->find();
        
        
        if(empty($info)){
            $info=$this
            ->alias('p')
            ->field($field)
            ->join('cmf_goods_brand_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->find();
        }
        if(empty($info)){
            return [];
        }
        $info=$info->getData();
        return $info;
    }
}

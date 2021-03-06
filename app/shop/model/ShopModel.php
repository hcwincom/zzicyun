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
     
        $field='p.id,p.logo,p.type,p.name as name,p.char,p.brands,p.pros';
        $order='p.sort asc';
        if(empty($limit)){
            $list=$this
            ->alias('p')
            ->where($where)
            ->order($order)
            ->column($field);
        }else{
            $list=$this
            ->alias('p')
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->column($field);
        }
       
        
        
        return $list;
    }
    /**
     * 获取供应商详情
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_one($lan=1,$lan1=1,$id){
        $where=['p.id'=>$id];
        //$field='p.id,p.logo,p.type,p.show_time,p.goods_num,p.code,val.name as name,val.char,val.brands,val.pros,val.dsc';
        
       
        $info=$this
        ->alias('p') 
        ->where($where) 
        ->find();
         
        if(empty($info)){
            return [];
        }
        $info=$info->getData();
        return $info;
    }
    
    /**
     * 获取最新供应商
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_new3($lan=1,$lan1=1,$where=['p.status'=>2],$limit=3){
        
        $field='p.id,p.logo,p.type,p.name as name,p.dsc';
        $order='p.show_time desc';
        
        $list=$this
        ->alias('p')
        ->where($where)
        ->order($order)
        ->limit($limit)
        ->column($field);
        
        return $list;
    }
    
}

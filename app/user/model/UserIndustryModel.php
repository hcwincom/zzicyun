<?php

namespace app\user\model;

use think\Db;
use think\Model;
/**
 *  用户行业
 *
 */
class UserIndustryModel extends Model
{
    public function get_all($lan1,$lan2){
        $field='p.id,val.name';
        $order='p.sort asc';
        $where=['p.status'=>2];
        $where['val.lid']=$lan1;
        $list=$this
        ->alias('p')
        ->join('cmf_user_industry_val val','val.pid=p.id and val.lid='.$lan1)
        ->where($where)
        ->order($order)
        ->column($field);
        if(empty($list0)){
            $where['val.lid']=$lan1;
            $list=$this
            ->alias('p')
            ->join('cmf_user_industry_val val','val.pid=p.id and val.lid='.$lan2)
            ->where($where)
            ->order($order)
            ->column($field);
        }
        
        return $list;
    }
    public function get_name($lan1,$lan2,$id){
       
        $where=['pid'=>$id,'lid'=>$lan1]; 
        $name=Db::name('user_industry_val')->where($where)->value('name');
        if(empty($name)){
            $name=Db::name('user_industry_val')->where($where)->value('name');
        }
        
        return $name;
    }
 
}

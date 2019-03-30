<?php
 
namespace app\notice\model;

use think\Model;
use think\Db;
class NoticeConfValModel extends Model
{
     /**
      * 获取指定语言所有配置信息
      * @param number $lan
      * @param number $lan1
      * @param array $where
      * @return $list
      */
    public function get_all_by_lan($lan=1,$lan1=1,$where=[]){
       
        $field='*'; 
        $where['lid']=$lan;
        $list0=$this 
        ->where($where) 
        ->column($field);
        if(empty($list0)){
            $where['lid']=$lan1;
            $list0=$this
            ->where($where)
            ->column($field);
        }
        $list=[];
        foreach($list0 as $k=>$v){
            $list[$v['conf']][$v['key']]=$v['val'];
        }
        return $list;
    }
    /**
     * 获取指定语言的指定配置信息
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @return $list
     */
    public function get_one($lan,$lan1,$conf){
        
        $field='key,val';
        $where=['conf'=>$conf];
        $where['lid']=$lan;
        $list0=$this
        ->where($where)
        ->column($field);
        if(empty($list0)){
            $where['lid']=$lan1;
            $list0=$this
            ->where($where)
            ->column($field);
        }
        
        return $list0;
    }
    /**
     * 获取指定配置所有语言信息
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @return $list
     */
    public function get_all_by_conf($conf){
        
        $field='*';
        $where=['conf'=>$conf];
        $list0=$this
        ->where($where)
        ->column($field);
        
        $list=[];
        foreach($list0 as $k=>$v){
            $list[$v['key']][$v['lid']]=$v['val'];
        }
        return $list;
    }
    
}

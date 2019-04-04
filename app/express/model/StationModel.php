<?php
 
namespace app\express\model;

use think\Model;
use think\Db;
class StationModel extends Model
{
     
    /**
     * 获取全部自提点
     * @return array
     */
    public function get_all($type){
        
        $where=[
            'p.type'=>$type,
            'p.status'=>2, 
        ];
      
        $field='p.*';
        $order='p.sort asc';
        $list=$this
        ->alias('p') 
        ->where($where)
        ->order($order)
        ->column($field);
        
        return $list;
         
    }
    /**
     * 获取自提点信息
     * @return array
     */
    public function get_info($id){
        
         
        $info=$this->where('id',$id)->find();
        
        return $info->getData();
        
    }
    
    
}

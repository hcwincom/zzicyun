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
    
    
}

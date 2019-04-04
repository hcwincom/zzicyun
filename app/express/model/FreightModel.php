<?php
 
namespace app\express\model;

use think\Model;
use think\Db;
class FreightModel extends Model
{
     
    /**
     * 获取全部快递
     * @return array
     */
    public function get_all($type){
        
        $where=[
            'p.type'.$type=>1,
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
     *  快递名称
     * @return $name
     */
    public function get_name($id,$lan1=1,$lan2=1){
        
       $name=$this->where('id',$id)->value('name');
        
       return $name;
        
    }
    
    
}

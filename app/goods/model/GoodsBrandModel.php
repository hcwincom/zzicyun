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
}

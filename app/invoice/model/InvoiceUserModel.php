<?php
 
namespace app\invoice\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandModel;
class InvoiceUserModel extends Model
{
     /**
      * 按类型获取用户发票
      * @param int $uid用户
      * @param int $type发票类型
      * @return unknown
      */
    public function get_infos_by_type($uid,$type){
         $where=[
             'uid'=>$uid, 
             'type'=>$type,
         ];
         if($type>0){
             $where['type']=$type;
         }
         $list=$this->where($where)->column('');
          return $list;
     }
     /**
      * 获取用户发票
      * @param int $uid
      * @return array[]
      */
     public function get_infos_all($uid){
         $where=[
             'uid'=>$uid,
         ];
         
         $list0=$this->where($where)->column('');
         $list=[];
         foreach($list0 as $k=>$v){
             $list[$v['type']][]=$v;
         }
         if(empty($list[1])){
             $list[1]=[];
         }
         if(empty($list[2])){
             $list[2]=[];
         }
         if(empty($list[3])){
             $list[3]=[];
         }
         return $list;
     }
    
}

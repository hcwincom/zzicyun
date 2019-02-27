<?php
 
namespace app\goods\model;

use think\Model; 
class GoodsPriceModel extends Model
{
    public function get_all($pid){
        $res=$this->where('pid',$pid)->order('num asc')->column('');
        return $res;
    }
    public function get_all_by_pids($goods_ids){
        $res=$this->where('pid','in',$goods_ids)->order('pid asc,num asc')->column('');
        $list=[];
        foreach($res as $k=>$v){
            $list[$v['pid']][]=$v;
        }
        return $list;
    }
     
}

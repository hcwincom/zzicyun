<?php

namespace app\money\model;

use think\Db;
use think\Model;

class CreditGetModel extends Model
{
    /**
     *授信记录
     */
    public function get_list($uid,$type=0){
        $where=['uid'=>$uid];
        if($type>0){
            $where['type']=$type;
            $list=$this->where($where)->column('');
        }else{
            $list0=$this->where($where)->column('');
            $list=[];
            foreach($list0 as $k=>$v){
                $list[$v['type']][$k]=$v;
            }
        } 
        return $list;
    }
    /**
     *授信添加
     */
    public function num_do($uid,$num,$dsc,$type,$aid=1){
        
        $data_add=[
            'uid'=>$uid,
            'num'=>$num,
            'type'=>$type,
            'time'=>time(),
            'dsc'=>$dsc,
            'aid'=>$aid,
        ];
        $this->insert($data_add);
        Db::name('user')->where('id',$uid)->setInc('credit'.$type,$num);
        return 1;
    }
 
}

<?php
 
namespace app\express\model;

use think\Model;
use think\Db;
class AreaModel extends Model
{
     
    /**
     * 获取全部下属地区 
     * @return array
     */
    public function get_all($fid=0,$lan1=1,$lan2=1){
        if($fid==-1){
            $fid=0;
        }
        $where=[
            'p.fid'=>$fid,
            'p.status'=>2,
            'val.lid'=>$lan1,
        ];
        $order='p.sort asc';
        $field='val.pid,val.name';
        $list=$this
        ->alias('p')
        ->join('cmf_area_val val','val.pid=p.id and val.lid='.$lan1)
        ->where($where)
        ->order($order)
        ->column($field);
        if(empty($list)){
            $where['val.lid']=$lan2;
            $list=$this
            ->alias('p')
            ->join('cmf_area_val val','val.pid=p.id and val.lid='.$lan2)
            ->where($where)
            ->order($order)
            ->column($field);
         }
        return $list;
    }
    /**
     * 获取全部地址名称
     * @return array
     */
    public function get_infos_by_ids($ids,$lan1=1,$lan2=1){
        $where=[
            'id'=>['in',$ids],
            'lid'=>$lan1
        ];
       
        $field='pid,name';
        $list=$this->where($where)->column($field);
        if(empty($list)){
            $where['lid']=$lan2;
            $list=$this->where($where)->column($field);
       }
        return $list;
    }
    
    /**
     * 获取地址全称
     * $is_country是否显示国家，默认显示
     * @return array
     */
    public function get_addressinfo($data,$lan1=1,$lan2=1,$is_country=1){
        if(empty($data['city'])){
            $fids=$this->get_fids_by_id($data['area']);
            if(empty($fids['city'])){
                return 0;
            }else{
                $data['country']=$fids['country'];
                $data['province']=$fids['province'];
                $data['city']=$fids['city'];
            } 
        }
        if(empty($data['country']) ){
            $data['country']=1;
        }
        $where=[
            'pid'=>['in',[$data['country'],$data['province'],$data['city'],$data['area']]],
            'lid'=>$lan1
        ];
        
        $field='pid,name';
        $m=Db::name('area_val');
        $list=$m->where($where)->column($field);
        if(empty($list)){
            $where['lid']=$lan2;
            $list=$m->where($where)->column($field);
        }
        if($is_country){
            $addressinfo=empty($list[$data['country']])?'':$list[$data['country']];
        }else{
            $addressinfo='';
        }
        
        $addressinfo.=empty($list[$data['province']])?'':$list[$data['province']];
        $addressinfo.=empty($list[$data['city']])?'':$list[$data['city']];
        $addressinfo.=empty($list[$data['area']])?'':$list[$data['area']];
       
        return $addressinfo;
    }
    /**
     * 获取地区的上级
     * @return array
     */
    public function get_fids_by_id($id){
         $data=[];
         $info=$this->where('id',$id)->find();
         $fid=$info['fid'];
         switch($info['type']){ 
            case 1: 
                $data['country']=$fid; 
                break;
            case 2: 
                $data['province']=$fid; 
                $data['country']=$this->where('id',$data['province'])->value('fid');
                break;
            case 3: 
                $data['city']=$fid; 
                $data['province']=$this->where('id',$data['city'])->value('fid');
                $data['country']=$this->where('id',$data['province'])->value('fid');
                break;
        }
        
        return $data;
    }
    
}

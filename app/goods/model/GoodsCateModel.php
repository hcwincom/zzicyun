<?php
 
namespace app\goods\model;

use think\Model;
use think\Db;
class GoodsCateModel extends Model
{
    /**
     * 获取所有分类，前台
     * @param number $lan语言
     * @param number $lan1空语言对应
     * @param string $field获取字段
     * @param array $where查询条件
     * @return array 
     */
    public function all_cates($lan=1,$lan1=1,$where=['p.status'=>2],$field='p.id,p.fid,p.pic,p.rate,p.num,val.val as name'){
        
        $list=$this
        ->alias('p')
        ->join('cmf_goods_cate_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where)
        ->order('p.rate asc,p.sort asc')
        ->column($field);
        if(empty($list)){
            $list=$this
            ->alias('p')
            ->join('cmf_goods_cate_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->order('p.rate asc,p.sort asc')
            ->column($field);
        }
        $cates=[];
        foreach($list as $k=>$v){
           switch($v['rate']){
               case 1:
                   $cates['cate1'][$v['id']]=$v;
                   break;
               case 2:
                   $cates['cate2'][$v['fid']][$v['id']]=$v;
                   break;
               case 3:
                   $cates['cate3'][$v['fid']][$v['id']]=$v;
                   break;
            }
        }
        return $cates;
    }
    /**
     * 获取所有分类，后台
     * @param number $lan语言
     * @param number $lan1空语言对应
     * @param string $field获取字段
     * @param array $where查询条件
     * @return array
     */
    public function all_cates1($where=['status'=>2],$field='id,name,fid,rate,num'){
        
        $list=$this
        ->where($where)
        ->order('rate asc,sort asc')
        ->column($field);
        
        $cates=[];
        foreach($list as $k=>$v){
            switch($v['rate']){
                case 1:
                    $cates['cate1'][$v['id']]=$v;
                    break;
                case 2:
                    $cates['cate2'][$v['id']]=$v;
                    break;
                case 3:
                    $cates['cate3'][$v['id']]=$v;
                    break;
            }
        }
        return $cates;
    }
    /**
     * 获取产品所属的分类名和上级分类id
     * @param number $cid分类id 
     * @return array
     */
    public function cate_info($cid){
        
        $info=$this->alias('c3')
        ->join('cmf_goods_cate c2','c2.id=c3.fid')
        ->join('cmf_goods_cate c1','c1.id=c2.fid')
        ->field('c1.name as cname1,c1.id as cid1,c2.name as cname2,c2.id as cid2,c3.name as cname3,c3.id as cid3')
        ->where('c3.id',$cid)
        ->find();
        return $info;
    }
    /**
     * 设置分类下产品的数量
     * @param number $cid分类id
     * @return array
     */
    public function set_number_by_cate3($cid){
        //获取最新产品数
        $where=['cid'=>$cid,'status'=>2];
        $nums=Db::name('goods')->where($where)->count();
        if(empty($nums)){
            $nums=0;
        }
        $this->where('id',$cid)->setField('num',$nums);
        //获取父级，计算产品数
        $cid2=$this->where('id',$cid)->value('fid');
        if(empty($cid2)){
            return 1;
        }
        $nums2=$this->where('fid',$cid2)->sum('num');
        if(empty($nums2)){
            $nums2=0;
        }
        $this->where('id',$cid2)->setField('num',$nums2);
        //获取父级，计算产品数
        $cid1=$this->where('id',$cid2)->value('fid');
        if(empty($cid1)){
            return 1;
        }
        $nums1=$this->where('fid',$cid1)->sum('num');
        if(empty($nums1)){
            $nums1=0;
        }
        $this->where('id',$cid1)->setField('num',$nums1);
        return 1;
    }
    /**
     * 获取分类下所有子类
     * @param number $cid分类id
     * @return array
     */
    public function get_cids_by_fid($cid){
        //获取最新产品数
        
        $rate=$this->where('id',$cid)->value('rate');
        $ids=[];
        switch($rate){ 
            case 2:
                $ids=$this->where('fid',$cid)->column('id'); 
                break;
            case 1:
                $ids=$this->where('fid',$cid)->column('id'); 
                if(!empty($ids)){
                    $ids3=$this->where('fid','in',$ids)->column('id'); 
                    $ids=array_merge($ids,$ids3);
                } 
                break;
        }
        $ids[]=$cid;
         
        return $ids;
    }
    /**
     * 重新统计分类下产品的数量 
     * @return number
     */
    public function set_number_all(){
        $list=$this->column('id,rate,fid,num');
        $cates=[];
        foreach($list as $k=>$v){
            switch($v['rate']){
                case 1:
                    $cates['cate1'][$v['id']]=$v;
                    break;
                case 2:
                    $cates['cate2'][$v['fid']][$v['id']]=$v;
                    break;
                case 3:
                    $cates['cate3'][$v['fid']][$v['id']]=$v;
                    break;
            }
        }
        //获取最新产品数
       
        $m_goods=Db::name('goods');
         
        foreach($cates['cate1'] as $k1=>$v1){
            if(empty( $cates['cate2'][$k1])){
                $nums=0;
            }else{
                foreach($cates['cate2'][$k1]as $k2=>$v2){
                    if(empty( $cates['cate3'][$k2])){
                        $nums=0;
                    }else{
                        //各分类一个个统计
                        foreach($cates['cate3'][$k2] as $k3=>$v3){
                            $where=['status'=>2,'cid'=>$k3];
                            $nums=$m_goods->where($where)->count();
                            if(empty($nums)){
                                $nums=0;
                            }
                            if($nums!=$v3['num']){
                                $this->where('id',$k3)->setField('num',$nums);
                            }
                        }
                        //统计二级分类
                        $nums=$this->where('fid',$k2)->sum('num');
                        if(empty($nums)){
                            $nums=0;
                        }
                    }
                    //二级分类是否有变化
                    if($nums!=$v2['num']){
                        $this->where('id',$k2)->setField('num',$nums);
                    } 
                }
                //统计一级分类
                $nums=$this->where('fid',$k1)->sum('num');
                if(empty($nums)){
                    $nums=0;
                }
            }
            //一级分类是否有变化
            if($nums!=$v1['num']){
                $this->where('id',$k1)->setField('num',$nums);
            } 
        }
        
        return 1;
    }
}

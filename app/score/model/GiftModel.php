<?php
 
namespace app\score\model;

use think\Model; 

class GiftModel extends Model
{
     /**
      * 按分类获取前列的礼品
      */
    public function get_list($cates,$limit=4){
        
        $list=[];
        //现获取热搜产品
        $where=[
            'status'=>2,
        ];
        $field='id,name,num_use,score,cid,pic';
        $list[0]=$this->where($where)->order('search desc,sort asc')->limit($limit)->column($field);
        //按分类获取不同产品
        foreach($cates as $k=>$v){
            $where['cid']=$k;
            $list[$k]=$this->where($where)->order('search desc,sort asc')->limit($limit)->column($field);
        }
       
        return $list;
    }
    /**
     * 按where获取礼品
     */
    public function get_page($where,$data,$page_row=12){
        
        $list=[]; 
        $field='id,name,num_use,score,cid,pic';
        $list=$this->where($where)->order('search desc,sort asc')->paginate($page_row);
       
        // 获取分页显示
        $page = $list->appends($data)->render();
        return ['list'=>$list,'page'=>$page];
    }
    /**
     * 获取礼品详情
     */
    public function get_one($id){
        
        $info=$this->where('id',$id)->find();
        $info=$info->getData();
        return $info;
    }
    
    
}

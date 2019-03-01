<?php
 
namespace app\notice\model;

use think\Model;
use think\Db;
class BannerModel extends Model
{
     /**
      * 获取banner图
      * @param string $cname分类 
      * @return $list
      */
    public function get_banners_by_cname($cname){
       $where=[
           'banner.status'=>2,
           'bc.name'=>$cname
       ];
       $list=$this
        ->alias('banner')
        ->join('cmf_banner_cate bc','bc.id=banner.cid')
        ->where($where)
        ->order('banner.sort asc')
        ->column('banner.id,banner.pic,banner.url,bc.num');
        if(empty($list)){
            return [];
        }
        //判断数量是否超出
         $list0=current($list);
         if($list0['num']>0 && $list0['num']<(count($list))){
             $i=0;
             //多余的要删除
             foreach($list as $k=>$v){
                 $i++;
                 if($i>$list0['num']){
                     unset($list[$k]);
                 }
             }
         }
        return $list;
    }
   
}

<?php
 
namespace app\notice\model;

use think\Model;
use think\Db;
class ArticleCateModel extends Model
{
     /**
      * 获取文章分类
      * @param number $lan
      * @param number $lan1
      * @param number $limit
      * @return $list
      */
    public function get_all($lan=1,$lan1=1,$where=['p.status'=>2]){
       
        $field='p.id,p.name,val.name as lan_name,val.dsc';
        $order='p.sort asc';
        $list=$this
        ->alias('p')
        ->join('cmf_article_cate_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where)
        ->order($order) 
        ->column($field,'p.name');
        if(empty($list)){
            $list=$this
            ->alias('p')
            ->join('cmf_article_cate_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->order($order) 
            ->column($field,'p.name');
        }
        $cates=['notice','news'];
        foreach($cates as $k=>$v){
            if(empty($list[$v])){
                $list[$v]=[
                    'id'=>0,
                    'name'=>'',
                    'lan_name'=>'',
                    'dsc'=>'',
                ];
            }
        }
        
        return $list;
    }
   
}

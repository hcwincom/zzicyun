<?php
 
namespace app\notice\model;

use think\Model;
use think\Db;
class ArticleModel extends Model
{
     /**
      * 获取部分文章标题
      * @param number $lan
      * @param number $lan1
      * @param number $limit
      * @return $list
      */
    public function get_limit($lan=1,$lan1=1,$where=['p.status'=>2],$limit=7){
       
        $field='p.id,p.clicked,p.ctime,p.pic,val.name';
        $order='p.sort asc,p.clicked desc,p.ctime desc';
        $list=$this
        ->alias('p')
        ->join('cmf_article_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where)
        ->order($order)
        ->limit($limit)
        ->column($field);
        if(empty($list)){
            $list=$this
            ->alias('p')
            ->join('cmf_article_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->column($field);
        }
      
        return $list;
    }
   
}

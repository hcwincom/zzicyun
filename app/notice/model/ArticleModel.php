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
    /**
     * 获取文章详情
     * @param number $lan
     * @param number $lan1
     * @param number $id
     * @return $info
     */
    public function get_info($lan=1,$lan1=1,$id){
         
        $field=['name','content'];
        $where=['id'=>$id];
        $info=$this->where('id',$id)->find();
        if(empty($info)){
            return null;
        }
        $info=$info->getData();
        $where=[
            'pid'=>$id,
            'lid'=>$lan,
        ];
        $val_info=Db::name('cmf_article_val')->where($where)->find();
        if(empty($val_info)){
            $where['lid']=$lan1;
            $val_info=Db::name('cmf_article_val')->where($where)->find();
        }
        //多语言值
        foreach($field as $k=>$v){ 
            $info[$v]=(isset($val_info[$v]))?$val_info[$v]:'';
        }
        
        return $info;
    }
    /**
     * 获取文章详情和上一篇，下一篇
     * @param number $lan
     * @param number $lan1
     * @param number $id
     * @return $last
     */
    public function get_infos($lan=1,$lan1=1,$id){
        
       
        $where=['id'=>$id];
        $info=$this->where('id',$id)->find();
        if(empty($info)){
            return null;
        }
        $info=$info->getData();
        $ids=[$id];
        //上一篇
        $order='id desc';
        $where=['status'=>2,'cid'=>$info['cid'],'id'=>['lt',$id]];
        $last=$this->where($where)->order($order)->value('id');
        if(empty($last)){
            $last=0; 
        }else{
            $ids[]=$last;
        }
       
        //下一篇
        $order='id asc';
        $where['id']=['gt',$id];
        $next=$this->where($where)->order($order)->value('id');
        if(empty($next)){
            $next=0;
        }else{
            $ids[]=$next;
        }
        
        $where=[
            'pid'=>['in',$ids],
            'lid'=>$lan,
        ];
        $vals=Db::name('cmf_article_val')->where($where)->column('*','pid');
        if(empty($vals)){
            $where['lid']=$lan1;
            $vals=Db::name('cmf_article_val')->where($where)->column('*','pid');
        }
        //多语言值
        $list=[];
        //当前
        if(isset($vals[$id])){
            $info['name']=$vals[$id]['name'];
            $info['content']=$vals[$id]['content'];
            $list['current']=$info;
        }else{
            
            $info['content']='';
            $list['current']=$info;
        }
        //上一个
        if(isset($vals[$last])){ 
            $list['last']=[
                'id'=>$last,
                'name'=>$vals[$last]['name'],
                'content'=>$vals[$last]['content'],
            ];
        }else{ 
            $list['last']=null;
        }
        //下一个
        if(isset($vals[$next])){
            $list['next']=[
                'id'=>$next,
                'name'=>$vals[$next]['name'],
                'content'=>$vals[$next]['content'],
            ];
        }else{
            $list['next']=null;
        }
         
        
        return $list;
    }
   
}

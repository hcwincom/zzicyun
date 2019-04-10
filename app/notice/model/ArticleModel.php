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
       
        $field='p.id,p.clicked,p.is_hot,p.ctime,p.pic,val.name';
        $order='p.is_hot asc,p.sort asc,p.clicked desc,p.ctime desc';
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
     * 获取文章标题
     * @param number $lan
     * @param number $lan1
     * @param array $cids
     * @return $list
     */
    public function get_names_by_cids($lan=1,$lan1=1,$cids){
        //获取底部的文章名称cid=[7,8,9,10]
        $where_cate=['p.id'=>['in',$cids],'p.status'=>2];
        // $articles=Db::name('articel')
        $m_articel_cate=new ArticleCateModel();
        $field_cate='p.id,val.name as lan_name';
        $order_cate='p.sort asc';
        $cates=$m_articel_cate
        ->alias('p')
        ->join('cmf_article_cate_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where_cate)
        ->order($order_cate)
        ->column($field_cate,'p.id');
        $field='p.id,val.name,p.cid';
        $order='p.cid asc,p.sort asc';
        $where=['p.cid'=>['in',$cids],'p.status'=>2];
       
        if(empty($cates)){
            $cates=$this
            ->alias('p')
            ->join('cmf_article_cate_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where_cate)
            ->order($order_cate)
            ->column($field_cate,'p.id');
            $list=$this
            ->alias('p')
            ->join('cmf_article_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->order($order)
            ->column($field);
        }else{
            $list=$this
            ->alias('p')
            ->join('cmf_article_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->column($field);
        }
       
        $list0=[]; 
        if(!empty($list)){  
            foreach($list as $k=>$v){
                if(isset($cates[$v['cid']])){ 
                    $list0[$cates[$v['cid']]][$k]=$v['name'];  
                } 
            }
        }  
        return $list0;
    }
    /**
     * 获取文章列表和分页
     * @param number $lan
     * @param number $lan1
     * @param number $limit
     * @return ['list'=>$list,'page'=>$pages];
     */
    public function get_page($lan=1,$lan1=1,$data,$page=15){
        $field_array=[
            'id','name','clicked','is_hot','ctime','pic'
        ];
        $field_str=implode(',', $field_array); 
        $order='is_hot asc,sort asc,clicked desc,ctime desc';
        $where=['cid'=>$data['cid'],'status'=>2];
        $ids0=$this->field($field_str)->where($where)->order($order)->paginate($page);
        if(empty($ids0)){
            return ['list'=>[],'page'=>null];
        }
        $pages=$ids0->appends($data)->render();
        $ids=[];
        $list=[];
        foreach($ids0 as $k=>$v){
            $ids[]=$v['id'];
            $list[$v['id']]=[];
            foreach($field_array as $vv){
                $list[$v['id']][$vv]=$v[$vv];
            }
            
        }
        $m_val=Db::name('article_val');
        $where=[
            'pid'=>['in',$ids],
            'lid'=>$lan
        ];
        $field_val='pid,name,content';
        $vals=$m_val->where($where)->column($field_val);
       
        if(empty($vals)){
            $where=[
                'pid'=>['in',$ids],
                'lid'=>$lan1
            ];
            $vals=$m_val->where($where)->column($field_val);
        }
        foreach($list as $k=>$v){
           if(isset($vals[$k])){
               
               $list[$k]['content']='';
           }else{
               $list[$k]['name']=$vals[$k]['name'];
               //过滤文字
               $list[$k]['content']=zz_get_content[$vals[$k]['content']];
           }
       }
       return ['list'=>$list,'page'=>$pages];
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
        $vals=Db::name('article_val')->where($where)->column('*','pid');
        if(empty($vals)){
            $where['lid']=$lan1;
            $vals=Db::name('article_val')->where($where)->column('*','pid');
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
            ];
        }else{ 
            $list['last']=null;
        }
        //下一个
        if(isset($vals[$next])){
            $list['next']=[
                'id'=>$next,
                'name'=>$vals[$next]['name'], 
            ];
        }else{
            $list['next']=null;
        }
         
        
        return $list;
    }
   
    //点击加一
    public function clicked($id){
       $this->where('id',$id)->setInc('clicked',1);
    }
}

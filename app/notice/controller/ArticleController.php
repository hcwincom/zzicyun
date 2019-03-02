<?php
 
namespace app\notice\controller;
 
use app\common\controller\DeskBaseController;
 
use think\Db; 
use app\notice\model\ArticleModel;
use app\notice\model\ArticleCateModel;
 
class ArticleController extends DeskBaseController
{

    /*文章详情  */
    public function article_detail()
    {
         $lan1=session('lan1');
         $lan2=session('lan2');
          $id=$this->request->param('id',0,'intval');
          
         $m_article=new ArticleModel();
         
         $article=$m_article->get_infos($lan1,$lan2,$id);
         if(empty($article)){
             $this->redirect(url('portal/Index/index'));
         }
         //分类
         $m_article_cate=new ArticleCateModel();
         $cate_info=$m_article_cate->get_info($lan1,$lan2,$article['current']['cid']);
          
         $this->assign('info',$article);
         
         $this->assign('cate_info',$cate_info);
          //clicked+1
          $m_article->clicked($id);
          
         return $this->fetch();
   }
   /*列表*/
   public function article_list()
   {
       $lan1=session('lan1');
       $lan2=session('lan2');
       $data=$this->request->param();
       if(empty($data['cid'])){
           $this->redirect('/');
       }
       //分类
       $m_article_cate=new ArticleCateModel();
       $cate_info=$m_article_cate->get_info($lan1,$lan2,$data['cid']);
       //获取列表
       $m_article=new ArticleModel();
       $list=$m_article->get_page($lan1,$lan2,$data);
       $this->assign('list',$list['list']);
       $this->assign('page',$list['page']);
       $this->assign('cate_info',$cate_info);
       return $this->fetch('article_list_'.$cate_info['name']);
   }
}

<?php
 
namespace app\portal\controller;
 
use app\common\controller\DeskBaseController;
use app\goods\model\GoodsCateModel;
use think\Db; 
use app\notice\model\ArticleModel;
use app\notice\model\ArticleCateModel;
use app\goods\model\GoodsFileModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
class ArticleController extends DeskBaseController
{

    /*文章详情  */
    public function detail()
    {
         $lan1=session('lan1');
         $lan2=session('lan2');
          $id=$this->request->param('id',0,'intval');
         //资讯5*7
         $m_article_cate=new ArticleCateModel();
         $articel_cates=$m_article_cate->get_all($lan1,$lan2);
       
         $m_article=new ArticleModel();
         
         $article=$m_article->get_infos($lan1,$lan2,$id);
         if(empty($article)){
             $this->redirect(url('portal/Index/index'));
         }
         //分类
         $m_article_cate=new ArticleCateModel();
         $cate_info=$m_article_cate->get_info($lan1,$lan2,$article['cid']);
          
         $this->assign('info',$article);
         
         $this->assign('cate_info',$cate_info);
          //查找上一篇下一篇
          
         return $this->fetch();
   }

}

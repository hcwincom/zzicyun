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
class IndexController extends DeskBaseController
{

    
    public function index()
    {
       
         $lan1=session('lan1');
         $lan2=session('lan2');
         //分类获取
         $m_cate=new GoodsCateModel();
         $cates=$m_cate->all_cates($lan1,$lan2);
         $this->assign('cate',$cates);
         //广告图
         $banner0=Db::name('banner')
         ->alias('banner')
         ->join('cmf_banner_cate bc','bc.id=banner.cid')
         ->where('banner.status',2)
         ->order('banner.sort asc')
         ->column('banner.id,banner.pic,banner.url,bc.num,bc.name as cname,banner.name');
         $banner=[];
         foreach($banner0 as $k=>$v){
             if(isset($banner[$v['cname']])){
                 if($v['num']==0 || count($banner[$v['cname']])<$v['num']){
                     $banner[$v['cname']][]=$v;
                 }
             }else{
                 $banner[$v['cname']][]=$v;
             }
         }
         $this->assign('banners',$banner);
         
         //商城公告6*5
         //资讯5*7
         $m_article_cate=new ArticleCateModel();
         $articel_cates=$m_article_cate->get_all($lan1,$lan2);
       
         $m_article=new ArticleModel();
         //商城公告6*5 
         $where=['p.status'=>2,'p.cid'=>$articel_cates['notice']['id']];
         $limit=5;
         $article_notice=$m_article->get_limit($lan1,$lan2,$where,$limit);
         
          
         $this->assign('article_notice',$article_notice);
         //资讯5*7 
         $where=['p.status'=>2,'p.cid'=>$articel_cates['news']['id']];
         $limit=7;
         $article_news=$m_article->get_limit($lan1,$lan2,$where,$limit);
         
         $this->assign('article_news',$article_news);
         $this->assign('articel_cates',$articel_cates);
         
         //数据手册*8
         $m_file=new GoodsFileModel();
         $pdfs=$m_file->get_limit();
         $this->assign('pdfs',$pdfs);
         //合作品牌
         //品牌还是品牌供应商，暂定为品牌
         $m_brand=new GoodsBrandModel();
         $brands=$m_brand->where('status',2)->order('sort asc')->limit(10)->column('id,pic,name');
         $this->assign('brands',$brands);
         
         return $this->fetch();
   }

}

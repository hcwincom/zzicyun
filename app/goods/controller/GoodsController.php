<?php
 
namespace app\goods\controller;
 
use app\common\controller\DeskBaseController;
use app\goods\model\GoodsCateModel;
use think\Db; 
use app\notice\model\ArticleModel;
use app\notice\model\ArticleCateModel;
use app\goods\model\GoodsFileModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
class GoodsController extends DeskBaseController
{

    
    public function detail()
    {
         $lan1=session('lan1');
         $lan2=session('lan2');
         $id=$this->request->param('id',0,'intval');
           
         //合作品牌
        
         //海量产品
         $m_goods=new GoodsModel();
         $goods=$m_goods->get_one($id,$lan1,$lan2);
         $this->assign('info',$goods);
         dump($goods);
         return $this->fetch();
   }

}

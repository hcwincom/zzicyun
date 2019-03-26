<?php
 
namespace app\order\controller;
 
use app\goods\model\GoodsCateModel;
use think\Db; 
use app\notice\model\ArticleModel;
use app\notice\model\ArticleCateModel;
use app\goods\model\GoodsFileModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\common\controller\UserBaseController;
class ApplyController extends UserBaseController
{
 
   public function myapply()
   {
      
       return $this->fetch();
   }
   public function apply_info()
   {
       
       return $this->fetch();
   }
   

}

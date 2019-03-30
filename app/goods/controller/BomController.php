<?php
 
namespace app\goods\controller;
 
 
use app\goods\model\GoodsCateModel;
use think\Db; 
  
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandCateModel;
use app\goods\model\GoodsParamModel;
use app\goods\model\GoodsTimeModel;
use app\goods\model\GoodsPriceModel;
use app\goods\model\GoodsFileModel;
use app\common\controller\UserBaseController;
class BomController extends UserBaseController
{

    
    /**
     * bom
      */
    public function bom()
    {
        $this->error('暂未开放',url('/'));
         $lan1=$this->lan1;
         $lan2=$this->lan2;
         $this->assign('html','bom');
         return $this->fetch();
   }
   /**
    * bom
    */
   public function mybom()
   {
       $this->error('暂未开放',url('/'));
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $this->assign('html','bom');
       return $this->fetch();
   }
   

}

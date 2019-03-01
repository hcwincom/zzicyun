<?php
 
namespace app\goods\controller;
 
use app\common\controller\DeskBaseController;
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
class BomController extends DeskBaseController
{

    
    /**
     * bom
      */
    public function bom()
    {
         $lan1=$this->lan1;
         $lan2=$this->lan2;
         $this->assign('html','bom');
         return $this->fetch();
   }
   

}

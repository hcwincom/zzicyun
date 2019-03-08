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
class ApplyController extends UserBaseController
{

    
    /**
     * 样品申请
      */
    public function index()
    {
         $lan1=$this->lan1;
         $lan2=$this->lan2;
         $this->assign('html','apply_index');
         return $this->fetch();
   }
   

}

<?php
 
namespace app\coupon\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandModel;
class CouponModel extends Model
{
     /**
      * 获取可领取的优惠券
      */
    public function get_list(){
        $where=[
            'type'=>2,
            'status'=>2,
             
        ];
    }
    
    
}

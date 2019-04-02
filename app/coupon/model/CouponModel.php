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
    public function get_page($page_row=20){
        $where=[
            'type'=>2,
            'status'=>2,
             
        ];
        $list0=$this->where($where)->order('sort asc')->paginate($page_row);
        $list=[];
        foreach($list0 as $k=>$v ){
            $tmp=[
                'id'=>$v['id'],
                'name'=>$v['dsc'],
                'dsc'=>$v['dsc'],
                'count'=>$v['count'],
                'count_get'=>$v['count_get'],
                'num'=>$v['num'],
                'money'=>$v['money'],
                'money_min'=>$v['money_min'],
                'money_type'=>$v['money_type'],
                'time1'=>$v['time1'],
                'time2'=>$v['time2'],
                'day'=>$v['day'],
            ];
            $list[$v['id']]=$tmp;
        }
        $page=$list0->render();
        
        return ['list'=>$list,'page'=>$page];
    }
   
    
    
}

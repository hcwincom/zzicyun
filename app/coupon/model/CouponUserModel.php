<?php
 
namespace app\coupon\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandModel;
class CouponUserModel extends Model
{
     /**
      * 获取可用优惠券
      * $uid用户，$type人民币1美元2
      */
    public function get_list($uid,$type,$use_ok=1){
        $where=[
            'uid'=>$uid, 
            'status'=>2
        ];
        if($use_ok==1){
            $where['status_use']=1;
            $where['status_time']=2;
        }
        $list=$this->where($where)->column('');
        if($type>1 && !empty($list)){
            //获取美元汇率
            $rate=Db::name('shop')->where('id',1)->value('rate');
            //得到供应商和品牌id
            foreach($list as $k=>$v){
                //价格计算
                $list[$k]['money']=round($v['money']*$rate,5);
                $list[$k]['money_min']=round($v['money_min']*$rate,5);
            }
        }
        
        
        return $list;
    }
    
    
}

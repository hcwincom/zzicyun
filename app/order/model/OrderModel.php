<?php
 
namespace app\order\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
use app\goods\model\GoodsBrandModel; 
use app\common\controller\UserBaseController;
use app\order\model\OrderCartModel;
use app\goods\model\GoodsTimeModel;
use app\coupon\model\CouponUserModel;
use app\user\model\UserAddressModel;
use app\express\model\StationModel;
use app\express\model\FreightModel;
use app\invoice\model\InvoiceUserModel;
use app\express\model\FreightAreaModel;
use app\user\model\UserModel;
use app\order\model\OrderModel;
class OrderModel extends Model
{
     //订单添加
    public function order_add($data,$uid,$lan1,$lan2){
        
        $price_type=($data['type']==1)?1:2;
        $uid=session('user.id');
        $data_order=[
            'uid'=>$uid,
            'coupon_id'=>$data['coupon_id'],//优惠券id
            'invoice_id'=>$data['invoice_id'],//发票资料id
            'freight_type'=>$data['freight_type'],//1快递2自提
            'address_id'=>$data['address_id'],//快递地址
            'station_id'=>$data['station_id'],// 自提地址
            'station_accept_name'=>$data['station_accept_name'],//自提人
            'station_accept_tel'=>$data['station_accept_tel'],//自提电话
            'freight_id'=>$data['freight_id'],//选择快递 
            'pay_type1'=>$data['pay_type1'],//在线支付
            'pay_type2'=>$data['pay_type2'],//支付宝
            'pay_type3'=>$data['pay_type3'],//全额付款比例
            'udsc'=>$data['udsc'],//订单备注 
            'type'=>$data['type'],//页面 
        ];
        //产品数据获取
        $m_goods=new GoodsModel();
        $res=$m_goods->goods_count($data['nums'],$lan1,$lan2,$price_type);
        $goods_list=$res['goods_list'];
        $count=$res['count'];
        $data_order['goods_num']=$count['num'];
        $data_order['goods_money']=$count['money'];
        $data_order['goods_weight']=$count['weight'];
        $data_order['goods_size']=$count['size'];
        
        //优惠券检测
        if(empty($data_order['coupon_id'])){
            $data_order['coupon_money']=0;
        }else{
            $m_coupon=new CouponUserModel();
            $coupon=$m_coupon->get_one($data_order['coupon_id'],$price_type);
            if(empty($coupon) || $coupon['uid']!=$uid || $coupon['money_min']<$count['money']){
                $data_order['coupon_id']=0;
                $data_order['coupon_money']=0;
            }else{
                $data_order['coupon_money']=$coupon['money'];
            }
        }
        //收货地址
        $m_address=new UserAddressModel();
        $address=$m_address->get_one($uid,$data['address_id']);
        $data_order['country']=$address['country'];
        $data_order['province']=$address['province'];
        $data_order['city']=$address['city'];
        $data_order['area']=$address['area'];
        $data_order['address_info']=$address['address_info'];
        $data_order['address']=$address['address'];
        //检测运费是否正确
        if($data_order['freight_type']==1 && $data_order['pay_type1']==1){
            $m_freight_area=new FreightAreaModel();
            $fee=$m_freight_area->get_fee($data_order['freight_id'], $data_order['city'], $data_order['goods_weight'], $data_order['goods_size'],$data_order['goods_money']-$data_order['coupon_money'],$data_order['type']);
            if($fee<0){
                return ('快递不配送');
            }else{
                $data_order['freight_pay']=$fee;
            }
        }else{
            $data_order['freight_pay']=0;
        }
        //比较费用是否变化
        if($data_order['goods_money']!=$data['goods_money'] || $data_order['coupon_money']!=$data['coupon_money'] || $data_order['freight_pay']!=$data['freight_pay']){
            return ('费用有变化，请重新检查提交');
        }
        $data_order['order_pay']=round($data_order['goods_money']-$data_order['coupon_money']+$data_order['freight_pay'],2);
        switch($data_order['pay_type3']){
            case 1:
                $data_order['pay1']=$data_order['order_pay'];
                $data_order['pay2']=0;
                break;
            case 2:
                $data_order['pay1']=round($data_order['order_pay']*0.3,2);
                $data_order['pay2']=round($data_order['order_pay']*0.7,2);
                break;
            case 3:
                $data_order['pay1']=round($data_order['order_pay']*0.5,2);
                $data_order['pay2']=round($data_order['order_pay']*0.5,2);
                break;
        }
        
        $time=time();
        $data_order['name']=$time.$uid;
        $data_order['create_time']=$time;
        //24小时内付款
        $data_order['pay_time_end']=$time+3600*24;
        $oid=$this->insertGetId($data_order);
        
        //添加产品
        $data_goods=[];
        foreach($goods_list as $k=>$v){
            $data_goods[]=[
                'oid'=>$oid,
                'goods'=>$k,
                'goods_name'=>$v['name'],
                'goods_code'=>$v['code'],
                'store_code'=>$v['store_code'],
                'price'=>$v['price'],
                'num'=>$v['num'],
                'pay'=>$v['order_price'],
                'udsc'=>$data['udscs'][$k]
            ];
        }
        Db::name('order_goods')->insertAll($data_goods);
        
        return $oid;
    }
    
    public function get_page($data,$uid=0,$page_row=5){
        $where=[];
        if($uid>0){
            $where['o.uid']=$uid;
        }
        $list=$this
        ->alias('o')
        ->join('cmf_order_goods og','og.oid=o.id')
        ->where($where) 
        ->paginate($page_row); 
        // 获取分页显示
        $page = $list->appends($data)->render();
        return ['list'=>$list,'page'=>$page];
    }
}

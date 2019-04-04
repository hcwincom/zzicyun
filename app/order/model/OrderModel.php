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
                'num'=>$v['order_num'],
                'pay'=>$v['order_price'],
                'udsc'=>$data['udscs'][$k]
            ];
        }
        Db::name('order_goods')->insertAll($data_goods);
        
        return $oid;
    }
    
    public function get_page($data,$where,$uid=0,$page_row=5){
        
        if($uid>0){
            $where['o.uid']=$uid;
        }
        
        $list=$this
        ->alias('o')
        ->join('cmf_order_goods og','og.oid=o.id')
        ->where($where) 
        ->field('o.id')
        ->paginate($page_row); 
        // 获取分页显示
        $page = $list->appends($data)->render();
        $orders=[];
        $oids=[];
        foreach($list as $k=>$v){
            $oids[]=$v['id'];
           /*  $orders[$v['id']]=[
                'id'=>$v['id'],
                'name'=>$v['name'],
                'type'=>$v['type'],
                'status'=>$v['status'],
                'accept_name'=>$v['accept_name'],
                'coupon_money'=>$v['coupon_money'],
                'goods_money'=>$v['goods_money'],
                'order_pay'=>$v['order_pay'],
                'create_time'=>$v['create_time'],
                'send_time'=>$v['send_time'],
                'accept_time'=>$v['accept_time'],
                'create_time'=>$v['create_time'],
                'completion_time'=>$v['completion_time'],
                'udsc'=>$v['udsc'],
                'is_back'=>$v['is_back'],
                'pay_id'=>$v['pay_id'],
                'invoice_status'=>$v['invoice_status'],
                'pay_type1'=>$v['pay_type1'],
                'pay_type2'=>$v['pay_type2'],
                'pay_type3'=>$v['pay_type3'],
                'pay_status'=>$v['pay_status'],
                'pay_review'=>$v['pay_review'],
                'pay_time_end'=>$v['pay_time_end'],
                'pay_time2'=>$v['pay_time2'],
                'pay_time1'=>$v['pay_time1'],
                'pay1'=>$v['pay1'],
                'pay2'=>$v['pay2'],
                'freight_pay'=>$v['freight_pay'],
                'freight_type'=>$v['freight_type'],
                'freight_id'=>$v['freight_id'],
                'goods_num'=>$v['goods_num'],
                'goods_list'=>[]
            ];  */
        }
        if(empty($oids)){
            $orders=[];
        }else{
            $orders=$this->where('id','in',$oids)->column('');
            $goods_list=Db::name('order_goods')->where('oid','in',$oids)->column('');
            foreach($goods_list as $k=>$v){
                $orders[$v['oid']]['goods_list'][$v['goods']]=$v;
            }
        }
        
        return ['list'=>$orders,'page'=>$page];
    }
    //订单添加
    public function order_pay_info($oid,$uid){
        $where=['id'=>$oid,'uid'=>$uid];
        $info=$this->where($where)->find();
        if(empty($info)){
             return [];
         }
         return $info->getData();
    }
    //订单添加
    public function order_info($oid,$uid){
        
        $info=$this->order_pay_info($oid,$uid);
        if(empty($info)){
            return [];
        }
        //付款方式
        $pay_type1s=config('pay_type1');
        //在线付款方式
        $pay_type2s=config('pay_type2');
        //付款金额比例
        $pay_type3s=config('pay_type3');
        $info['pay_type1_name']=isset($pay_type1s[$info['pay_type1']])?$pay_type1s[$info['pay_type1']]:$info['pay_type1'];
        $info['pay_type2_name']=isset($pay_type2s[$info['pay_type2']])?$pay_type2s[$info['pay_type2']]:$info['pay_type2'];
        $info['pay_type3_name']=isset($pay_type3s[$info['pay_type3']])?$pay_type3s[$info['pay_type3']]:$info['pay_type3']; 
        if($info['freight_type']==1){
            $m_freight=new FreightModel();
            $info['freight_name']=$m_freight->get_name($info['freight_id']);
        }else{
            $m_station=new StationModel();
            $station=$m_station->get_info($info['station_id']);
            $info['station_address']=empty($station)?'':($station['address_info'].$station['address']);
        }
        if(!empty($info['invoice_id'])){
            $m_invoice=new InvoiceUserModel();
            $info['invoice_info']=$m_invoice->get_info($info['invoice_id'], $info['uid']);
            if(!empty($info['invoice_info'])){
                $invoice_types=config('invoice_type');
                $info['invoice_info']['type_name']=isset($invoice_types[$info['invoice_info']['type']])?$invoice_types[$info['invoice_info']['type']]:$info['invoice_info']['type']; 
            }
           
        }
        $order_statuss=config('order_status');
        $info['status_name']=isset($order_statuss[$info['status']])?$order_statuss[$info['status']]:$info['status']; 
        return $info;
    }
}

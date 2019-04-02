<?php
 
namespace app\order\controller;
  
use think\Db; 
 
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\common\controller\UserBaseController;
use app\order\model\OrderCartModel;
use app\goods\model\GoodsTimeModel;
use app\coupon\model\CouponUserModel;
use app\user\model\UserAddressModel;
use app\express\model\StationModel;
use app\express\model\FreightModel;
use app\invoice\model\InvoiceUserModel;
use app\express\model\FreightAreaModel;
class OrderController extends UserBaseController
{

    
    
   public function myorder()
   {
      
      
       return $this->fetch();
   }
   public function order_info()
   {
       
       return $this->fetch();
   }

   //购物车转化为订单
   public function cart_order(){
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       //goods,num,type,coupon
       $data=$this->request->param();
       if(empty($data['type'])){ 
           $data['type']=1;
       }
       if(empty($data['coupon'])){
           $data['coupon']=0;
       }
       if(empty($data['ids'])){
          $this->error('error_data');
       } 
       $uid=session('user.id');
       $where=[
           'id'=>['in',$data['ids']],
           'uid'=>$uid
       ];
       $m_cart= new OrderCartModel();
       $nums=$m_cart->where($where)->column('goods,num');
       $this->order_do1($nums,$data['type'],$data['coupon']);
       return $this->fetch('order_prepare');
   }
   //转化为订单页面
   public function order_do1($nums,$type=1,$coupon=0){
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $goods_ids=array_keys($nums);
       $price_type=($type==1)?1:2;
       $uid=session('user.id');
      
       $m_goods=new GoodsModel();
       $res=$m_goods->goods_list($lan1,$lan2,$goods_ids);
       $goods_list=$res['goods_list'];
       $price_list=$res['price_list'];
       $shop_ids=[];
       $brand_ids=[];
       //累加产品数量,金额,重量
       $count_num=0;
       $count_money=0;
       $count_weight=0;
       $count_size=0;
       //产品信息，获取价格
       foreach($goods_list as $k=>$v){
            
           $v['price']=$v['price'.$price_type];
           $v['goods_time']=$v['goods_time'.$price_type];
           $v['order_num']=$nums[$k];
         
           if(isset($price_list[$k])){ 
               foreach($price_list[$k] as $kk=>$vv){
                   if($v['num']>=$vv['num']){
                       $v['price']=$vv['price'.$price_type];
                   }
               }
           } 
           $v['order_price']=round($nums[$k]* $v['price'],2);
          
           $goods_list[$k]=$v; 
           $brand_ids[$v['brand']]=$v['brand'];
           //累加产品数量,金额,重量
           $count_num+=$v['order_num'];
           $count_money+=$v['order_price'];
           $count_weight+=$nums[$k]*$v['weight'];
           $count_size+=$nums[$k]*$v['size'];
       }
       $count_money=round($count_money,2);
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2,['p.id'=>['in',$brand_ids]]);
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
      
       //优惠券
       if($type>2){
           $coupons=[];
       }else{
           $m_coupon=new CouponUserModel(); 
           $coupons=$m_coupon->get_list($uid,$price_type); 
           
       }
       $this->assign('goods_list',$goods_list); 
       $this->assign('brands',$brands);
       $this->assign('goods_times',$goods_times);
       $this->assign('coupons',$coupons);
       $this->assign('type',$type);
       $this->assign('coupons',$coupons);
       $this->assign('coupon',$coupon);
       $this->assign('money_flag',($type==1)?'￥':'$');
      
       $this->assign('count_num',$count_num);
       $this->assign('count_money',$count_money);
       $this->assign('count_weight',$count_weight);
       $this->assign('count_size',$count_size); 
       
       //收货地址
       $m_address=new UserAddressModel();
       $addresses=$m_address->get_all($uid,$type);
       $this->assign('addresses',$addresses);
       //快递和自提选择
       $m_station=new StationModel();
       $stations=$m_station->get_all($type);
       $m_freight=new FreightModel();
       $freights=$m_freight->get_all($type);
       $this->assign('stations',$stations);
       $this->assign('freights',$freights);
       //付款方式
       $pay_type1s=config('pay_type1');
       //在线付款方式
       $pay_type2s=config('pay_type2');
       //付款金额比例
       $pay_type3s=config('pay_type3');
       $this->assign('pay_type1s',$pay_type1s);
       $this->assign('pay_type2s',$pay_type2s);
       $this->assign('pay_type3s',$pay_type3s);
       //发票
       $m_invoice=new  InvoiceUserModel();
       $invoices=$m_invoice->get_infos_all($uid);
       $this->assign('invoices',$invoices);
       //运费计算
       $m_fr=new FreightAreaModel();
       $freight=current($freights);
       $address=current($addresses);
       $fee=$m_fr->get_fee($freight['id'], $address['city'], $count_weight, $count_size,$count_money,$freight['size']);
       $this->assign('freight_fee',$fee);
       $this->assign('freight_id',$freight['id']);
       $this->assign('address_id',$address['id']);
       
       $money_end=round($fee+$count_money,2);
       $this->assign('money_end',$money_end);
      
   }
   //转化为订单页面
   public function order_do2(){
       $data0=$this->request->param();
       dump($data0);
       $data=[
           'coupon_id'=>1,//优惠券
           'invoice_id'=>1,
           'freight_type'=>1,//1快递2自提
           'address_id'=>1,//快递和自提地址
           'accept_name'=>'',//自提人
           'tel'=>'',//自提电话 
           'pay_freight'=>0,//运费
           'freight_id'=>1,//选择快递
           'pay_type1'=>1,//在线支付
           'pay_type2'=>1,//支付宝
           'pay_type3'=>1,//全额付款
           'dsc'=>'速度快点',//订单备注
           'nums'=>[],//产品数量
           'dscs'=>[],//产品备注
           'goods'=>[],//产品ids
           'pay_freight'=>0,//运费
       ];
   }
   //运费计算
   public function freight_fee(){
       $data=$this->request->param();
       $m_freight_area=new FreightAreaModel();
       $fee=$m_freight_area->get_fee($data['freight_id'], $data['city'], $data['weight'], $data['size'],$data['money'],$data['type']);
       if($fee>=0){
           $this->success($fee);
       }else{
           $this->error('快递不配送');
       }
        
   }
   
}

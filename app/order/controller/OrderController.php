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
use app\user\model\UserModel;
use app\order\model\OrderModel;
class OrderController extends UserBaseController
{

    
    
   public function myorder()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $uid=session('user.id');
       $data=$this->request->param();
       $m_order=new OrderModel();
       $where=['o.uid'=>$uid];
       //待审核(2)
       $where1=['uid'=>$uid,'pay_status'=>['in',[2,5]]];
       // 待付款(2)
       $where2=['uid'=>$uid,'pay_status'=>['in',[1,4]]];
       // 待发货(2)
       $where3=['uid'=>$uid,'status'=>['in',[2,3,4]]];
       // 待收货(2)
       $where4=['uid'=>$uid,'status'=>['in',[4,5]]];
       //已完成(2)
       $where5=['uid'=>$uid,'status'=>['in',[7,10,11]]];
       $data['count1']=$m_order->where($where1)->count();
       $data['count2']=$m_order->where($where2)->count();
       $data['count3']=$m_order->where($where3)->count();
       $data['count4']=$m_order->where($where4)->count();
       $data['count5']=$m_order->where($where5)->count();
       if(empty($data['status'])){
          $data['status']=0;
       } else{
           switch($data['status']){
               case 1:
                   $where['o.pay_status']=['in',[2,4]];
                   break;
               case 2:
                   $where['o.pay_status']=['in',[1,3]];
                   break;
               case 3:
                   $where['o.status']=['in',[2,3,4]];
                   break;
               case 4:
                   $where['o.status']=['in',[4,5]];
                   break; 
               case 5:
                   $where['o.status']=['in',[7,10,11]];
                   break;
           }
       }
       if(empty($data['order_status'])){
           $data['order_status']=0;
       }else{
           $where['o.status']=$data['order_status'];
       }
       if(empty($data['pay_status'])){
           $data['pay_status']=0;
       }else{
           $where['o.pay_status']=$data['pay_status'];
       }
       if(empty($data['send_status'])){
           $data['send_status']=0;
       }else{
           $where['og.send_status']=$data['send_status'];
       }
       if(empty($data['send_status'])){
           $data['send_status']=0;
       }else{
           $where['og.send_status']=$data['send_status'];
       }
       if(empty($data['goods_name'])){
           $data['goods_name']='';
       }else{
           $where['og.goods_name|og.goods_code|og.store_code']=['like','%'.$data['goods_name'].'%'];
       }
       //创建时间
       if(empty($data['time1'])){
           $data['time1']=''; 
           if(empty($data['time2'])){
               $data['time2']=''; 
           } else{
               $time2=strtotime($data['time2']);
               $where['o.create_time']=['lt',$time2];
           }
       } else{
           $time1=strtotime($data['time1']);
           if(empty($data['time2'])){
               $data['time2']='';
               $where['o.create_time']=['gt',$time1];
           } else{
               $time2=strtotime($data['time2']);
               $where['o.create_time']=['between',[$time1,$time2]];
           }
       }
       
       $this->assign('data',$data);
       $this->assign('status',$data['status']);
      
       $res=$m_order->get_page($data,$where,$uid);
       $orders=$res['list'];
       $this->assign('orders',$orders);
       $this->assign('page',$res['page']);
       //付款方式
       $pay_type1s=config('pay_type1');
       //在线付款方式
       $pay_type2s=config('pay_type2');
       //付款金额比例
       $pay_type3s=config('pay_type3');
       $send_statuss=config('send_status');
       $pay_statuss=config('pay_status');
       $order_statuss=config('order_status');
       $order_types=config('order_type');
      
       //
       
       $this->assign('pay_type1s',$pay_type1s);
       $this->assign('pay_type2s',$pay_type2s);
       $this->assign('pay_type3s',$pay_type3s);
       $this->assign('send_statuss',$send_statuss);
       $this->assign('pay_statuss',$pay_statuss);
       $this->assign('order_statuss',$order_statuss);
       $this->assign('order_types',$order_types);
       return $this->fetch();
   }
   public function order_info()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $id=$this->request->param('id',0,'intval');
       $uid=session('user.id');
       $m_order=new OrderModel();
       $info=$m_order->order_info($id,$uid);
       $order_statuss=config('order_status');
       $goods_list=Db::name('order_goods')->where('oid','eq',$id)->column('*','goods');
       
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2);
       foreach($goods_list as $k=>$v){
           if(isset($goods_times[$v['goods_time']])){ 
               $goods_list[$k]['time_name']=$goods_times[$v['goods_time']];  
           }else{ 
               $goods_list[$k]['time_name']=''; 
           }
           if(isset($brands[$v['brand']])){
               $goods_list[$k]['brand_name']=$brands[$v['brand']]['name'];
           }else{
               $goods_list[$k]['brand_name']=''; 
           }
           
       }
       $send_statuss=config('send_status');
       $this->assign('order',$info);
       $this->assign('goods_list',$goods_list);
       $this->assign('order_statuss',$order_statuss);
       $this->assign('send_statuss',$send_statuss);
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
   //直接购买
   public function buy_order(){
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       //goods,num,type,coupon
       $data=$this->request->param();
       if(empty($data['type'])){
           $data['type']=1;
       }
       $data['num']=1;
       $data['id']=2664;
       if(empty($data['id']) || empty($data['num'])){
           $this->error('error_data');
       }
       $uid=session('user.id');
       $where=[
           'id'=>['eq',$data['id']],
           'uid'=>$uid
       ];
        
       $nums=[$data['id']=>$data['num']];
       
       $this->order_do1($nums,$data['type']);
       return $this->fetch('order_prepare');
   }
   //转化为订单页面
   public function order_do1($nums,$type=1,$coupon_id=0){
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $goods_ids=array_keys($nums);
       $price_type=($type==1)?1:2;
       $uid=session('user.id');
      
       $m_goods=new GoodsModel();
       $res=$m_goods->goods_count($nums,$lan1,$lan2,$price_type);
       
       $goods_list=$res['goods_list'];
       $price_list=$res['price_list'];
       $count=$res['count']; 
       $brand_ids=$count['brand_ids'];
       //累加产品数量,金额,重量
       $count_num=$count['num'];
       $count_money=$count['money'];
       $count_weight=$count['weight'];
       $count_size=$count['size'];
        
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2,['p.id'=>['in',$brand_ids]]);
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
      
       //优惠券
       $coupon_money=0;
       $coupon_recommend=0;
       if($type>2){
           $coupons=[];
       }else{
           $m_coupon=new CouponUserModel(); 
           $coupons=$m_coupon->get_list($uid,$price_type);  
           if(isset($coupons[$coupon_id])){
               if($coupons[$coupon_id]['money_min']<$count['money']){
                   $coupon_money=$coupons[$coupon_id]['money'];
               }  
           }  
           //推荐优惠
           if($coupon_money==0){
               $coupon_id=0;
               $coupon_recommend=$m_coupon->get_recommend($uid, $count['money'],$price_type); 
           }   
       }
       
       $this->assign('goods_list',$goods_list); 
       $this->assign('brands',$brands);
       $this->assign('goods_times',$goods_times);
       $this->assign('coupons',$coupons);
       $this->assign('type',$type);
       $this->assign('coupons',$coupons);
       $this->assign('coupon_id',$coupon_id);
       $this->assign('coupon_recommend',$coupon_recommend);
       $this->assign('coupon_money',$coupon_money);
       $this->assign('money_flag',($type==1)?'￥':'$');
      
       $this->assign('count_num',$count_num);
       $this->assign('count_money',$count_money);
       $this->assign('count_weight',$count_weight);
       $this->assign('count_size',$count_size); 
       
       //收货地址
       $m_address=new UserAddressModel();
       $addresses=$m_address->get_all($uid,$type);
       $this->assign('addresses',$addresses);
       //快递和自提选择.3
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
       $fee=$m_fr->get_fee($freight['id'], $address['city'], $count_weight, $count_size,$count_money-$coupon_money,$type);
       if($fee<0){
           $fee=0;
           $freight_ok=0;
       }else{
           $freight_ok=1;
       }
       $this->assign('freight_fee',$fee);
       $this->assign('freight_ok',$freight_ok);
       $this->assign('freight_id',$freight['id']);
       $this->assign('address_id',$address['id']);
      
       $money_end=round($fee+$count_money-$coupon_money,2);
       $this->assign('money_end',$money_end);
      
   }
   //转化为订单页面
   public function order_do2(){
       $data=$this->request->param();
      
       $lan1=$this->lan1;
       $lan2=$this->lan2; 
       $uid=session('user.id'); 
        
       $m_order=new OrderModel();
       $m_order->startTrans();
       $res=$m_order->order_add($data,$uid,$lan1,$lan2);
      
       if($res>0){
           $m_order->commit();
           $this->redirect(url('order_pay',['id'=>$res]));
       }else{
           $m_order->rollback();
           $this->error($res);
       }
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
   
   public function order_pay(){
       $oid=$this->request->param('id',0,'intval');
       $uid=session('user.id');
       $m_order=new OrderModel();
       $order=$m_order->order_pay_info($oid, $uid);
       
       $this->assign('order',$order);
       
       return $this->fetch('order_pay4');
       
   }
   
}

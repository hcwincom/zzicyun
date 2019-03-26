<?php
 
namespace app\order\controller;
 
use app\goods\model\GoodsCateModel;
use think\Db; 
use app\notice\model\ArticleModel;
use app\notice\model\ArticleCateModel;
use app\goods\model\GoodsFileModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\common\controller\UserBaseController;
use app\common\controller\DeskBaseController;
use app\order\model\OrderCartModel;
use app\goods\model\GoodsTimeModel;
use app\coupon\model\CouponUserModel;
class CartController extends DeskBaseController
{
   
   public function mycart()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $data=$this->request->param();
       if(empty($data['type'])){
           $data['type']=1;
       }
       $uid=session('user.id');
       $session_id=session_id();
       //先检测是否存在
       $where=[
           'type'=>$data['type'], 
       ];
       if(empty($uid)){  
           $where['session_id']=$session_id;
       }else{
           $where['uid']=$uid;
       }
       $m_cart=new OrderCartModel();
       $goods_data=$m_cart->cart_page($lan1,$lan2,$where,$data);
       $list=$goods_data['list'];
       $page=$goods_data['page'];
       $brands=$goods_data['brands'];
       $shops=$goods_data['shops'];
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
       //优惠券
       if(empty($uid)){
           $coupons=[];
       }else{
           $m_coupon=new CouponUserModel();
           if($data['type']>1 || $lan1>1){
               $type=2;
           }else{
               $type=1;
           }
           $coupons=$m_coupon->get_list($uid,$type);
           
       }
       $this->assign('list',$list);
       $this->assign('page',$page);
       $this->assign('shops',$shops);
       $this->assign('brands',$brands);
       $this->assign('goods_times',$goods_times);
       $this->assign('coupons',$coupons);
       $this->assign('type',$data['type']);
       return $this->fetch();
   }

   //加入购物车
   public function cart_add(){
       $data=$this->request->param();
       $data=[
           'goods'=>intval($data['goods']),
           'num'=>intval($data['num']),
           'type'=>intval($data['type']),
       ];
       $uid=session('user.id');
       $session_id=session_id();
       //先检测是否存在
       $where=[
           'type'=>$data['type'],
           'goods'=>$data['goods'],
       ];
       if(empty($uid)){
           $uid=0;
           $where['uid']=$uid;
           $where['session_id']=$session_id;
       }
       
       $where['uid']=$uid;
       $m_cart=new OrderCartModel();
       $cart=$m_cart->where($where)->find();
       if(empty($cart)){
           //新增
           $data['uid']=$uid;
           $data['session_id']=$session_id;
           $m_cart->insert($data);
       }else{
           $m_cart->where($where)->setInc('num',$data['num']);
       }
       $this->success('ok');
   }
   //购物车修改
   public function cart_change(){
       $data=$this->request->param();
        
       $uid=session('user.id');
       $session_id=session_id();
       //先检测是否存在
       $where=[
           'id'=>$data['id'], 
       ];
       if(empty($uid)){ 
           $where['session_id']=$session_id;
       } 
       
       $where['uid']=$uid;
       $m_cart=new OrderCartModel();
       
       if(empty($data['type'])){
           //不是收货地更改就是删除或修改数量
           //删除
           if(empty($data['num'])){
               $m_cart->where($where)->delete();
           }else{
               //数量更新
               $m_cart->where($where)->setField('num',$data['num']);
           }
          
       }else{
           //收货地更新
           $m_cart->where($where)->setField('type',$data['type']);
       }
       $this->success('ok');
   }
   //购物车批量删除和修改
   public function cart_changes(){
       $data=$this->request->param();
       
       $uid=session('user.id');
       $session_id=session_id();
       //先确认where条件
       $ids=explode(',', $data['ids']);
       $where=[
           'id'=>['in',$ids],
       ];
       if(empty($uid)){
           $where['session_id']=$session_id;
       }
       
       $where['uid']=$uid;
       $m_cart=new OrderCartModel();
       
       if(empty($data['type'])){
           //不是收货地更改就是删除 
           //删除
           $m_cart->where($where)->delete(); 
       }else{
           //收货地更新
           $m_cart->where($where)->setField('type',$data['type']);
       }
       $this->success('ok');
   }
   
}

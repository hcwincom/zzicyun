<?php
 
namespace app\user\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController;
use app\user\model\UserModel;
use app\coupon\model\CouponUserModel;
use app\order\model\OrderModel;

class CenterController extends UserBaseController
{

    /**
     * 个人中心 
     */
    public function center()
    {
        $uid=session('user.id');
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
        session('user',$user);
        $count=[];
        $m_coupon_user=new CouponUserModel();
        $count['coupon']=$m_coupon_user->get_count(1); 
        
        $m_order=new OrderModel(); 
        // 待付款(2)
        $where2=['uid'=>$uid,'pay_status'=>['in',[1,4]]];
        // 待发货(2)
        $where3=['uid'=>$uid,'status'=>['in',[2,3,4]]];
        // 待收货(2)
        $where4=['uid'=>$uid,'status'=>['in',[4,5]]]; 
       
        $count['order2']=$m_order->where($where2)->count();
        $count['order3']=$m_order->where($where3)->count();
        $count['order4']=$m_order->where($where4)->count();
       
        $this->assign('user',$user);
        $this->assign('count',$count);
        return $this->fetch();
    }

     
}
<?php
 
namespace app\user\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController;
use app\user\model\UserModel;
use app\coupon\model\CouponUserModel;

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
        $count['order1']=2;
        $count['order2']=2;
        $count['order3']=2;
        $count['order4']=2;
        $count['order5']=2;
        
        $this->assign('user',$user);
        $this->assign('count',$count);
        return $this->fetch();
    }

     
}
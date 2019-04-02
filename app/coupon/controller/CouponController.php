<?php
 
namespace app\coupon\controller;

 
 
use think\Db; 
use app\coupon\model\CouponUserModel;
 
use app\common\controller\DeskBaseController;
use app\coupon\model\CouponModel;
//领券中心
class CouponController extends DeskBaseController
{

    public function _initialize()
    {
        
        parent::_initialize();
        $lan1=$this->lan1;
        if($lan1!=1){
            $this->redirect(url('/'));
        }
    }
    /**
     *优惠券
     */
    public function coupon_center()
    {
       
        
        $m_coupon=new CouponModel();
        
        $res=$m_coupon->get_page();
        $list=$res['list'];
        $ids=array_keys($list);
        
        $uid=session('user.id');
        if(empty($uid) || empty($ids)){
            $gets=[];
            $counts_get=[];
        }else{
            $m_coupon_user=new CouponUserModel();
            //统计领用数量
            $where=['uid'=>$uid,'coupon'=>['in',$ids],'status'=>2];
            $counts_get=$m_coupon_user->where($where)->group('coupon')->column('coupon,count(id)');
            //可用优惠券
            $where=['uid'=>$uid,'coupon'=>['in',$ids],'status'=>2,'status_use'=>1,'status_time'=>2];
            $gets=$m_coupon_user->where($where)->column('coupon,id');
        }
        foreach($list as $k=>$v){
            //继续领用
            if($v['count'] > 0 && $v['count']>$v['count_get']){
                $list[$k]['is_get']=0;
            }else{
                $list[$k]['is_get']=1;
            }
            //用户领用
            if(isset($counts_get[$k]) && $v['num'] > 0 && $v['num']<=$counts_get[$k]){
                $list[$k]['is_uget']=0;
            }else{
                $list[$k]['is_uget']=1;
            }
        }
        $this->assign('list',$list); 
        $this->assign('page',$res['page']);  
        $this->assign('gets',$gets);  
        dump($counts_get);
      dump($gets);
      dump($list);
 
        return $this->fetch();
    }
    
}
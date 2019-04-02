<?php
 
namespace app\coupon\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController; 
use app\user\model\UserModel;  
use app\money\model\CreditGetModel;
use app\money\model\CreditApplyModel;
use app\coupon\model\CouponUserModel;
use app\coupon\model\CouponModel;

class CouponUserController extends UserBaseController
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
    public function coupon_user()
    {
       
        $uid=session('user.id');
        $data=$this->request->param();
        $where=[
            'uid'=>$uid,
            'status_use'=>1,
        ];
         if(empty($data['status'])){
             $data['status']=1;
         } 
        
         $where1=['status_time'=>['lt',3]];
         $where2=['status_time'=>3];
        $m_coupon_user=new CouponUserModel();
        $data['count1']=$m_coupon_user->where($where)->where($where1)->count();
        $data['count2']=$m_coupon_user->where($where)->where($where2)->count();
        if($data['status']==3){
            $where=array_merge($where,$where2);
        }else{
            $where=array_merge($where,$where1);
        }
       
        $res=$m_coupon_user->get_page($where, $data);
      
        $this->assign('list',$res['list']); 
        $this->assign('page',$res['page']); 
        $this->assign('data',$data); 
      
        return $this->fetch();
    }
    /**
     *优惠券领用
     */
    public function coupon_get()
    {
        
        $uid=session('user.id');
        $id=$this->request->param('id',0,'intval');
        $m_coupon=new CouponUserModel();
        $m_coupon->startTrans();
        $res=$m_coupon->user_get($id,$uid);
        if(!($res>0)){
            $m_coupon->rollback();
            $this->error($res);
         }
         $m_coupon->commit();
         $this->success('ok'); 
    }
    
}
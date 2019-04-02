<?php
 
namespace app\coupon\controller;
 
use think\Db; 
 
use cmf\controller\HomeBaseController;
use app\coupon\model\CouponUserModel;
 
class TaskController extends HomeBaseController
{
 
     //优惠券过期检测,每日零点01执行
     public function coupon_old(){
         $time=time();
         $time_start=strtotime(date('Y-m-d',$time));
         $time_end=$time_start+3600*24-1;
         $m_coupon_user= new CouponUserModel(); 
         $dsc='系统任务，优惠券时间状态检测。';
         //可使用->过期,
         $where=[ 
             'status_use'=>1,
             'status_time'=>2,
             'time2'=>['elt',$time_end]
         ];
         $row=$m_coupon_user->where($where)->setField('status_time',3);
         $dsc.='更新优惠券过期'.$row.'条，';
         //不可使用-》可使用
         $where=[
             'status_use'=>1,
             'status_time'=>1,
             'time1'=>['elt',$time_start]
         ];
         $row=$m_coupon_user->where($where)->setField('status_time',2);
         $dsc.='更新优惠券可使用'.$row.'条，';
         //优惠券类型的过期禁用
         $where=[
             'status'=>2,
             'time1'=>['gt',0],
             'time2'=>['elt',$time_end]
         ];
         $row=Db::name('coupon')->where($where)->setField('status',4);
         $dsc.='更新优惠券类型过期'.$row.'条。';
         //记录操作记录
         $data_action=[
             'aid'=>1,
             'time'=>$time,
             'ip'=>get_client_ip(),
             'action'=>$dsc,
             'table'=>'system',
             'type'=>'edit',
             'pid'=>0,
             'link'=>'',
             'shop'=>1,
         ];
         Db::name('action')->insert($data_action);
         exit($dsc);
         
     }

}

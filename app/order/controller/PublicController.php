<?php
 
namespace app\order\controller;
 
use app\common\controller\DeskBaseController; 
use think\Db; 
use app\express\model\AreaModel;
use app\order\model\OrderCartModel;
 
class PublicController extends DeskBaseController
{
 
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
    //运费计算
    public function freight_fee(){
        
    }

}

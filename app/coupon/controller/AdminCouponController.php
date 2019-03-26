<?php

namespace app\coupon\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;

class AdminCouponController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='优惠券';
        $this->table='coupon';
        $this->m=Db::name('coupon');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','money_min'=>'round2','money'=>'round2',
            'type'=>'int','day'=>'int','time1'=>'str','time2'=>'str'];
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0;
       
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 优惠券列表
     * @adminMenu(
     *     'name'   => '优惠券列表',
     *     'parent' => 'coupon/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1,
     *     'icon'   => '',
     *     'remark' => '优惠券列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     * 优惠券添加
     * @adminMenu(
     *     'name'   => '优惠券添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();
        
    }
    /**
     * 优惠券添加do
     * @adminMenu(
     *     'name'   => '优惠券添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 优惠券详情
     * @adminMenu(
     *     'name'   => '优惠券详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 优惠券状态审核
     * @adminMenu(
     *     'name'   => '优惠券状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 优惠券状态批量同意
     * @adminMenu(
     *     'name'   => '优惠券状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 优惠券禁用
     * @adminMenu(
     *     'name'   => '信息状态禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '信息状态禁用',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        parent::ban();
    }
    /**
     * 优惠券信息状态恢复
     * @adminMenu(
     *     'name'   => '优惠券信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 优惠券编辑提交
     * @adminMenu(
     *     'name'   => '优惠券编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     * 优惠券编辑列表
     * @adminMenu(
     *     'name'   => '优惠券编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 优惠券审核详情
     * @adminMenu(
     *     'name'   => '优惠券审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 优惠券信息编辑审核
     * @adminMenu(
     *     'name'   => '优惠券编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 优惠券编辑记录批量删除
     * @adminMenu(
     *     'name'   => '优惠券编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 优惠券批量删除
     * @adminMenu(
     *     'name'   => '优惠券批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '优惠券批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        
        parent::del_all();
    }
    /**
     * 分类信息
     * {@inheritDoc}
     * @see \app\common\controller\AdminInfoController::cates()
     */
    public function cates($type=3){
        parent::cates($type);
        $this->assign('coupon_types',config('coupon_type'));
        
    }
    
    public function param_check(&$data){
        $res=parent::param_check($data);
        if($res!=1){
            return $res;
        }
        if(empty($data['time2'])){
            $data['time2']=0;
            $data['time1']=0;
            if(empty($data['day'])){
                $data['day']=10;
            }
        }else{
            $data['day']=0;
            if(empty($data['time1'])){
                $data['time1']=time();
            } else{
                $data['time1']=strtotime($data['time1']);
            }
            $data['time2']=strtotime($data['time2']);
            if($data['time2'] < $data['time1']){
                return '截止时间填写错误';
            }
        }
        return 1;
    }
     
}

<?php

namespace app\order\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;

class AdminCartController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='购物车';
        $this->table='order_cart';
        $this->m=Db::name('order_cart');
        $this->base=[];
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0;
       
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 购物车产品列表
     * @adminMenu(
     *     'name'   => '购物车产品列表',
     *     'parent' => 'order/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 20,
     *     'icon'   => '',
     *     'remark' => '购物车列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
   
    
    /**
     * 购物车详情
     * @adminMenu(
     *     'name'   => '购物车详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '购物车详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    
    /**
     * 分类信息
     * {@inheritDoc}
     * @see \app\common\controller\AdminInfoController::cates()
     */
    public function cates($type=3){
        parent::cates($type);
        
    }
     
}

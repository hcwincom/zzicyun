<?php
 
namespace app\store\controller;
 
use app\store\model\StoreOutModel;
 
/* 产品出库 */
class AdminStoreOutController extends StoreinBaseController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='产品出库';
        $this->table='store_out';
        $this->m=new StoreOutModel();
       
        $this->assign('flag',$this->flag);
        $this->about_type=config('store_out_type');
        $this->assign('about_types',$this->about_type);
         
    }
    /**
     * 产品出库列表
     * @adminMenu(
     *     'name'   => '产品出库列表',
     *     'parent' => 'store/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 81,
     *     'icon'   => '',
     *     'remark' => '产品出库列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
        parent::index();
        return $this->fetch();
    }
    
   
    /**
     * 产品出库添加
     * @adminMenu(
     *     'name'   => '产品出库添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品出库添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        
        return $this->fetch();
    }
    /**
     * 产品出库添加do
     * @adminMenu(
     *     'name'   => '产品出库添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品出库添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品出库详情
     * @adminMenu(
     *     'name'   => '产品出库详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品出库详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品出库状态审核
     * @adminMenu(
     *     'name'   => '产品出库状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品出库状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品出库状态还原
     * @adminMenu(
     *     'name'   => '产品出库状态还原',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品出库状态还原',
     *     'param'  => ''
     * )
     */
    public function review_back()
    {
        parent::review_back();
    }
    /**
     * 产品出库状态批量审核
     * @adminMenu(
     *     'name'   => '产品出库状态批量审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品出库状态批量审核',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    
}

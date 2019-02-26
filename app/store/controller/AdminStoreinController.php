<?php
 
namespace app\store\controller;

 
use think\Db; 
 
/* 产品入库 */
class AdminStoreinController extends AdminStoreinBaseController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='产品入库';
        $this->table='store_in';
        $this->m=Db::name('store_in');
       
        $this->assign('flag',$this->flag);
        $this->about_type=config('store_in_type');
        $this->assign('about_types',$this->about_type);
         
    }
    /**
     * 产品入库列表
     * @adminMenu(
     *     'name'   => '产品入库列表',
     *     'parent' => 'store/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 81,
     *     'icon'   => '',
     *     'remark' => '产品入库列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
        parent::index();
        return $this->fetch();
    }
    
   
    /**
     * 产品入库添加
     * @adminMenu(
     *     'name'   => '产品入库添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品入库添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        
        return $this->fetch();
    }
    /**
     * 产品入库添加do
     * @adminMenu(
     *     'name'   => '产品入库添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品入库添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品入库详情
     * @adminMenu(
     *     'name'   => '产品入库详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品入库详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品入库状态审核
     * @adminMenu(
     *     'name'   => '产品入库状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品入库状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品入库状态批量同意
     * @adminMenu(
     *     'name'   => '产品入库状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品入库状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    
}

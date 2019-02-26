<?php
 
namespace app\goods\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
/* 产品发货时间 */
class AdminGoodsTimeController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='产品发货时间';
        $this->table='goods_time';
        $this->m=Db::name('goods_time');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str',];
         
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'val'=>'多语言名称'
        ];
        $this->assign('flag','产品发货时间');
         
    }
    /**
     * 产品发货时间列表
     * @adminMenu(
     *     'name'   => '产品发货时间列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 81,
     *     'icon'   => '',
     *     'remark' => '产品发货时间列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
        parent::index();
        return $this->fetch();
    }
    
   
    /**
     * 产品发货时间添加
     * @adminMenu(
     *     'name'   => '产品发货时间添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        
        return $this->fetch();
    }
    /**
     * 产品发货时间添加do
     * @adminMenu(
     *     'name'   => '产品发货时间添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品发货时间详情
     * @adminMenu(
     *     'name'   => '产品发货时间详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品发货时间状态审核
     * @adminMenu(
     *     'name'   => '产品发货时间状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品发货时间状态批量同意
     * @adminMenu(
     *     'name'   => '产品发货时间状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 产品发货时间禁用
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
     * 产品发货时间信息状态恢复
     * @adminMenu(
     *     'name'   => '产品发货时间信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 产品发货时间编辑提交
     * @adminMenu(
     *     'name'   => '产品发货时间编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 产品发货时间编辑列表
     * @adminMenu(
     *     'name'   => '产品发货时间编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 产品发货时间审核详情
     * @adminMenu(
     *     'name'   => '产品发货时间审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 产品发货时间信息编辑审核
     * @adminMenu(
     *     'name'   => '产品发货时间编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 产品发货时间编辑记录批量删除
     * @adminMenu(
     *     'name'   => '产品发货时间编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品发货时间编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
     
    public function del_all()
    {
         $this->error('发货时间不能删除，可以修改，可以禁用');
        
    }
     
}

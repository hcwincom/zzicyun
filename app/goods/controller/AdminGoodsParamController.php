<?php

namespace app\goods\controller;


use think\Db; 
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsBrandModel;
/* 产品规格参数的添加 */
class AdminGoodsParamController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='产品规则参数';
        $this->table='goods_param';
        $this->m=Db::name('goods_param');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','is_lan'=>'int',];
        
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'val'=>'多语言名称',  
            
        ];
        $this->assign('flag','产品规则参数');
        
    }
    /**
     * 产品规则参数列表
     * @adminMenu(
     *     'name'   => '产品规则参数列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 31,
     *     'icon'   => '',
     *     'remark' => '产品规则参数列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
         parent::index();
        
        return $this->fetch();
    }
    
    
    /**
     * 产品规则参数添加
     * @adminMenu(
     *     'name'   => '产品规则参数添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
       
        return $this->fetch();
    }
    /**
     * 产品规则参数添加do
     * @adminMenu(
     *     'name'   => '产品规则参数添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品规则参数详情
     * @adminMenu(
     *     'name'   => '产品规则参数详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
      
        return $this->fetch();
    }
    /**
     * 产品规则参数状态审核
     * @adminMenu(
     *     'name'   => '产品规则参数状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品规则参数状态批量同意
     * @adminMenu(
     *     'name'   => '产品规则参数状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 产品规则参数禁用
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
     * 产品规则参数信息状态恢复
     * @adminMenu(
     *     'name'   => '产品规则参数信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 产品规则参数编辑提交
     * @adminMenu(
     *     'name'   => '产品规则参数编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 产品规则参数编辑列表
     * @adminMenu(
     *     'name'   => '产品规则参数编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 产品规则参数审核详情
     * @adminMenu(
     *     'name'   => '产品规则参数审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 产品规则参数信息编辑审核
     * @adminMenu(
     *     'name'   => '产品规则参数编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 产品规则参数编辑记录批量删除
     * @adminMenu(
     *     'name'   => '产品规则参数编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 产品规则参数批量删除
     * @adminMenu(
     *     'name'   => '产品规则参数批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规则参数批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        
        parent::del_all();
        
    }
    
    
}

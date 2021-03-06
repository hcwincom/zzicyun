<?php

namespace app\notice\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;

class AdminNoticeController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='页面提示信息';
        $this->table='notice';
        $this->m=Db::name('notice');
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=1;
        $this->vals=['val'=>'多语言提示'];
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 页面提示信息列表
     * @adminMenu(
     *     'name'   => '页面提示信息列表',
     *     'parent' => 'notice/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 2,
     *     'icon'   => '',
     *     'remark' => '页面提示信息列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     * 页面提示信息添加
     * @adminMenu(
     *     'name'   => '页面提示信息添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();
        
    }
    /**
     * 页面提示信息添加do
     * @adminMenu(
     *     'name'   => '页面提示信息添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 页面提示信息详情
     * @adminMenu(
     *     'name'   => '页面提示信息详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 页面提示信息状态审核
     * @adminMenu(
     *     'name'   => '页面提示信息状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 页面提示信息状态批量同意
     * @adminMenu(
     *     'name'   => '页面提示信息状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 页面提示信息禁用
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
     * 页面提示信息信息状态恢复
     * @adminMenu(
     *     'name'   => '页面提示信息信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 页面提示信息编辑提交
     * @adminMenu(
     *     'name'   => '页面提示信息编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     * 页面提示信息编辑列表
     * @adminMenu(
     *     'name'   => '页面提示信息编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 页面提示信息审核详情
     * @adminMenu(
     *     'name'   => '页面提示信息审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 页面提示信息信息编辑审核
     * @adminMenu(
     *     'name'   => '页面提示信息编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 页面提示信息编辑记录批量删除
     * @adminMenu(
     *     'name'   => '页面提示信息编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 页面提示信息批量删除
     * @adminMenu(
     *     'name'   => '页面提示信息批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示信息批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        $admin=$this->admin;
        if($admin['id']>1){
            $this->error('只有超级管理员super能删除');
        }
        parent::del_all();
    }
    /**
     * 分类信息
     * {@inheritDoc}
     * @see \app\common\controller\AdminInfoController::cates()
     */
    public function cates($type=3){
        parent::cates($type);
        //分类信息 
        $where=['status'=>2]; 
        $cates=Db::name('notice_cate')->where($where)->order('sort asc')->column('id,name');
       
        $this->assign('cates',$cates);
    }
    
}

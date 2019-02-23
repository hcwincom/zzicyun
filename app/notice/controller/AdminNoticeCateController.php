<?php

namespace app\notice\controller;


use app\common\controller\AdminInfoController;
use think\Db;

class AdminNoticeCateController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='页面提示类型';
        $this->table='notice_cate';
        $this->m=Db::name('notice_cate');
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0; 
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 页面提示类型列表
     * @adminMenu(
     *     'name'   => '页面提示类型列表',
     *     'parent' => 'notice/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1,
     *     'icon'   => '',
     *     'remark' => '页面提示类型列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     * 页面提示类型添加
     * @adminMenu(
     *     'name'   => '页面提示类型添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();
        
    }
    /**
     * 页面提示类型添加do
     * @adminMenu(
     *     'name'   => '页面提示类型添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 页面提示类型详情
     * @adminMenu(
     *     'name'   => '页面提示类型详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 页面提示类型状态审核
     * @adminMenu(
     *     'name'   => '页面提示类型状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 页面提示类型状态批量同意
     * @adminMenu(
     *     'name'   => '页面提示类型状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 页面提示类型禁用
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
     * 页面提示类型信息状态恢复
     * @adminMenu(
     *     'name'   => '页面提示类型信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 页面提示类型编辑提交
     * @adminMenu(
     *     'name'   => '页面提示类型编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     * 页面提示类型编辑列表
     * @adminMenu(
     *     'name'   => '页面提示类型编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 页面提示类型审核详情
     * @adminMenu(
     *     'name'   => '页面提示类型审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 页面提示类型信息编辑审核
     * @adminMenu(
     *     'name'   => '页面提示类型编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 页面提示类型编辑记录批量删除
     * @adminMenu(
     *     'name'   => '页面提示类型编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 页面提示类型批量删除
     * @adminMenu(
     *     'name'   => '页面提示类型批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面提示类型批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        if(empty($_POST['ids'])){
            $this->error('未选中信息');
        }
        $ids=$_POST['ids'];
        
        $m=$this->m;
        
        $admin=$this->admin;
        //其他店铺检查,如果没有shop属性就只能是1号主站操作,有shop属性就带上查询条件
        if($admin['shop']!=1){
            $this->error('店铺不能操作系统数据');
        }
       
        //彻底删除
        $where=['cid'=>['in',$ids]];
        //检查是否有用户
        $tmp=Db::name('notice')->where($where)->find();
        if(!empty($tmp)){
            $this->error('页面提示类型下有提示，不能删除');
        }
        parent::del_all();
    }
    
    
    
}

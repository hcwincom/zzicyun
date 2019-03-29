<?php

namespace app\score\controller;


use app\common\controller\AdminInfoController;
use think\Db; 
class AdminScoreController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='积分规则';
        $this->table='score_rule';
        $this->m=Db::name('score_rule');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','code'=>'str','score'=>'int',
            'type'=>'int','number'=>'int','rate'=>'round2'];
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0;
       
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 积分规则列表
     * @adminMenu(
     *     'name'   => '积分规则列表',
     *     'parent' => 'score/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1,
     *     'icon'   => '',
     *     'remark' => '积分规则列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     * 积分规则添加
     * @adminMenu(
     *     'name'   => '积分规则添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $admin=$this->admin;
        if($admin['id']>1){
            $this->error('只有超级管理员super能操作');
        }
        parent::add();
        return $this->fetch();
        
    }
    /**
     * 积分规则添加do
     * @adminMenu(
     *     'name'   => '积分规则添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        $admin=$this->admin;
        if($admin['id']>1){
            $this->error('只有超级管理员super能操作');
        }
        parent::add_do();
        
    }
    /**
     * 积分规则详情
     * @adminMenu(
     *     'name'   => '积分规则详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 积分规则状态审核
     * @adminMenu(
     *     'name'   => '积分规则状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 积分规则状态批量同意
     * @adminMenu(
     *     'name'   => '积分规则状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 积分规则禁用
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
     * 积分规则信息状态恢复
     * @adminMenu(
     *     'name'   => '积分规则信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 积分规则编辑提交
     * @adminMenu(
     *     'name'   => '积分规则编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     * 积分规则编辑列表
     * @adminMenu(
     *     'name'   => '积分规则编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 积分规则审核详情
     * @adminMenu(
     *     'name'   => '积分规则审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 积分规则信息编辑审核
     * @adminMenu(
     *     'name'   => '积分规则编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 积分规则编辑记录批量删除
     * @adminMenu(
     *     'name'   => '积分规则编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 积分规则批量删除
     * @adminMenu(
     *     'name'   => '积分规则批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '积分规则批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        $admin=$this->admin;
        if($admin['id']>1){
            $this->error('只有超级管理员super能操作');
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
        $this->assign('score_types',config('score_type'));
        
    }
    
    public function param_check(&$data){
        $res=parent::param_check($data);
        if($res!=1){
            return $res;
        }
        
        return 1;
    }
     
}

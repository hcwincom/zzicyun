<?php

namespace app\notice\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;

class AdminBannerController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='页面广告图';
        $this->table='banner';
        $this->m=Db::name('banner');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','url'=>'str','pic'=>'str','cid'=>'int'];
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0;
       
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 页面广告图列表
     * @adminMenu(
     *     'name'   => '页面广告图列表',
     *     'parent' => 'notice/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 21,
     *     'icon'   => '',
     *     'remark' => '页面广告图列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     * 页面广告图添加
     * @adminMenu(
     *     'name'   => '页面广告图添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();
        
    }
    /**
     * 页面广告图添加do
     * @adminMenu(
     *     'name'   => '页面广告图添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 页面广告图详情
     * @adminMenu(
     *     'name'   => '页面广告图详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 页面广告图状态审核
     * @adminMenu(
     *     'name'   => '页面广告图状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 页面广告图状态批量同意
     * @adminMenu(
     *     'name'   => '页面广告图状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 页面广告图禁用
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
     * 页面广告图信息状态恢复
     * @adminMenu(
     *     'name'   => '页面广告图信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 页面广告图编辑提交
     * @adminMenu(
     *     'name'   => '页面广告图编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     * 页面广告图编辑列表
     * @adminMenu(
     *     'name'   => '页面广告图编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 页面广告图审核详情
     * @adminMenu(
     *     'name'   => '页面广告图审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 页面广告图信息编辑审核
     * @adminMenu(
     *     'name'   => '页面广告图编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 页面广告图编辑记录批量删除
     * @adminMenu(
     *     'name'   => '页面广告图编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 页面广告图批量删除
     * @adminMenu(
     *     'name'   => '页面广告图批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '页面广告图批量删除',
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
        $cate=Db::name('banner_cate')->where('status',2)->order('sort asc')->column('id,name,dsc');
        $this->assign('banner_cates',$cate);
        
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        
        $table=$this->table;
        $pathid=$table.'/'.$id.'/';
        $data_update=[];
        $cate=Db::name('banner_cate')->where('id',$data['cid'])->find();
        $pic_conf=[
            'width'=>$cate['width'],
            'height'=>$cate['height'],
            'logo'=>0
        ];
        $data_update['pic']=zz_set_file($data['pic'],$pathid,$pic_conf);
        
        $m=$this->m;
        $m->where('id',$id)->update($data_update);
        
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        $m=$this->m;
         
        $table=$this->table;
        $pathid=$table.'/'.$data['id'].'/';
        $cate=Db::name('banner_cate')->where('id',$data['cid'])->find();
        $pic_conf=[
            'width'=>$cate['width'],
            'height'=>$cate['height'],
            'logo'=>0
        ];
         
        $data['pic']=zz_set_file($data['pic'],$pathid,$pic_conf);
       
        return 1;
    }
}

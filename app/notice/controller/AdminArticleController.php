<?php

namespace app\notice\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;

class AdminArticleController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='文章';
        $this->table='article';
        $this->m=Db::name('article');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','url'=>'str','pic'=>'str','cid'=>'int','ctime'=>'str'];
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=1;
        $this->vals=['name'=>'多语言标题','content'=>'多语言详情'];
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     *文章列表
     * @adminMenu(
     *     'name'   => '文章列表',
     *     'parent' => 'notice/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 31,
     *     'icon'   => '',
     *     'remark' => '文章列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     *文章添加
     * @adminMenu(
     *     'name'   => '文章添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
       
        parent::add();
        return $this->fetch();
        
    }
    /**
     *文章添加do
     * @adminMenu(
     *     'name'   => '文章添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     *文章详情
     * @adminMenu(
     *     'name'   => '文章详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     *文章状态审核
     * @adminMenu(
     *     'name'   => '文章状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     *文章状态批量同意
     * @adminMenu(
     *     'name'   => '文章状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     *文章禁用
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
     *文章信息状态恢复
     * @adminMenu(
     *     'name'   => '文章信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     *文章编辑提交
     * @adminMenu(
     *     'name'   => '文章编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     *文章编辑列表
     * @adminMenu(
     *     'name'   => '文章编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     *文章审核详情
     * @adminMenu(
     *     'name'   => '文章审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     *文章信息编辑审核
     * @adminMenu(
     *     'name'   => '文章编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     *文章编辑记录批量删除
     * @adminMenu(
     *     'name'   => '文章编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     *文章批量删除
     * @adminMenu(
     *     'name'   => '文章批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '文章批量删除',
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
        $cate=Db::name('article_cate')->where('status',2)->order('sort asc')->column('id,name,dsc');
        $this->assign('article_cates',$cate);
        //lans转化为json
        $lans=session('admin_lans');
        $this->assign('lans_json',json_encode($lans));
        
    }
    /**
     * 参数值过滤
     *   */
    public function param_check(&$data){
        parent::param_check($data);
        //发布时间
        if(empty($data['ctime'])){
            $data['ctime']=time();
        }else{
            $data['ctime']=strtotime($data['ctime']);
        }
       
        //编辑器免过滤
        foreach($data['lan-name'] as $k=>$v){
            $data['lan-content'][$k]=$_POST['lan-content-'.$k];
            unset($data['lan-content-'.$k]);
        }
      
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        
        $table=$this->table;
        $pathid=$table.'/'.$id.'/';
        $data_update=[];
     
        $pic_conf=config('pic_'.$table);
        $data_update['pic']=zz_set_file($data['pic'],$pathid,$pic_conf);
        
        $m=$this->m;
        $m->where('id',$id)->update($data_update);
        
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        $m=$this->m;
        
        $table=$this->table;
        $path1=$table.'/'.$data['id'].'/';
        $pic_conf=config('pic_'.$table);
        $data['pic']=zz_set_file($data['pic'],$path1,$pic_conf); 
          
        return 1;
    }
}

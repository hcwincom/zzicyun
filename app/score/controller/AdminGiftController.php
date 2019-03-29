<?php

namespace app\score\controller;


use app\common\controller\AdminInfoController;
use think\Db; 
use app\score\model\GiftCateModel;
class AdminGiftController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='礼品';
        $this->table='gift';
        $this->m=Db::name('gift');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','pic'=>'str','price'=>'round2',
            'cid'=>'int','brand'=>'str','code'=>'str','store_code'=>'str',
            'store_num'=>'int','num_use'=>'int','content'=>'str','score'=>'int','search'=>'int' 
        ];
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0;
       
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 礼品列表
     * @adminMenu(
     *     'name'   => '礼品列表',
     *     'parent' => 'score/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 11,
     *     'icon'   => '',
     *     'remark' => '礼品列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        parent::index();
        return $this->fetch();
    }
    
    
    /**
     * 礼品添加
     * @adminMenu(
     *     'name'   => '礼品添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        
        parent::add();
        return $this->fetch();
        
    }
    /**
     * 礼品添加do
     * @adminMenu(
     *     'name'   => '礼品添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        
        parent::add_do();
        
    }
    /**
     * 礼品详情
     * @adminMenu(
     *     'name'   => '礼品详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 礼品状态审核
     * @adminMenu(
     *     'name'   => '礼品状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 礼品状态批量同意
     * @adminMenu(
     *     'name'   => '礼品状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 礼品禁用
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
     * 礼品信息状态恢复
     * @adminMenu(
     *     'name'   => '礼品信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 礼品编辑提交
     * @adminMenu(
     *     'name'   => '礼品编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        
        parent::edit_do();
    }
    /**
     * 礼品编辑列表
     * @adminMenu(
     *     'name'   => '礼品编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 礼品审核详情
     * @adminMenu(
     *     'name'   => '礼品审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 礼品信息编辑审核
     * @adminMenu(
     *     'name'   => '礼品编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 礼品编辑记录批量删除
     * @adminMenu(
     *     'name'   => '礼品编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 礼品批量删除
     * @adminMenu(
     *     'name'   => '礼品批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '礼品批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        
        parent::del_all();
    }
    
    public function cates($type=3){
        parent::cates($type);
        $m_cate=new GiftCateModel();
        $cates=$m_cate->get_names();
        $this->assign('cates',$cates);
        $pic_conf=config('pic_gift');
        $this->assign('pic_conf',$pic_conf);
    }
    public function param_check(&$data){
        $res=parent::param_check($data);
        if(!($res>1)){
            return $res;
        }
        //$data['content']=$_POST['content'];
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        $table=$this->table;
        $path1=$table.'/'.$data['id'].'/';
        $pic_conf=config('pic_'.$table);
        $data['pic']=zz_set_file($data['pic'],$path1,$pic_conf);
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        
        $table=$this->table;
        $path1=$table.'/'.$id.'/';
        $data_update=[];
        
        $pic_conf=config('pic_'.$table);
        $data_update['pic']=zz_set_file($data['pic'],$path1,$pic_conf);
        
        $m=$this->m;
        $m->where('id',$id)->update($data_update);
        
        return 1;
    }
    
     
}

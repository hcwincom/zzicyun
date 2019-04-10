<?php

namespace app\money\controller;


use think\Db; 
use app\common\controller\AdminInfoController; 
use app\money\model\BankModel;
/* 银行的添加 */
class AdminBankController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='银行';
        $this->table='bank';
        $this->m=new BankModel();
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','pic'=>'str','is_show'=>'int'];
        
        $this->isshop=0;
        $this->islan=0;
        
        $this->assign('flag','银行');
        
    }
    /**
     * 银行列表
     * @adminMenu(
     *     'name'   => '银行列表',
     *     'parent' => 'money/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 80,
     *     'icon'   => '',
     *     'remark' => '银行列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
        $table=$this->table;
        $m=$this->m;
        $admin=$this->admin;
        
        $data=$this->request->param();
        $where=[];
        //店铺,分店只能看到自己的数据，总店可以选择店铺
        if($this->isshop){
            zz_shop($admin, $data, $where);
        }
        
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        //状态
        if(empty($data['fid'])){
            $data['fid']=0;
        }else{
            $arr=explode('-',$data['fid']);
            $data['fid']=$arr[0];
            $rate=$arr[1];
            if($rate==2){
                $where['fid']=['eq',$data['fid']];
            }else{
                $fids=$m->where('fid',$data['fid'])->column('id');
                $where['fid']=['in',$fids];
            }
        }
        
        //添加人
        if(empty($data['aid'])){
            $data['aid']=0;
        }else{
            $where['aid']=['eq',$data['aid']];
        }
        //审核人
        if(empty($data['rid'])){
            $data['rid']=0;
        }else{
            $where['rid']=['eq',$data['rid']];
        }
        
        //类型
        if(empty($data['type'])){
            $data['type']=0;
        }else{
            $where['type']=['eq',$data['type']];
        }
        //查询字段
        $types=$this->search;
        $search_types=config('search_types');
        zz_search_param($types,$search_types,$data,$where);
        
        
        //时间类别
        $times=config('pro_time_search');
        zz_search_time($times,$data,$where);
        
        $list=$m
        ->where($where)
        ->order('status asc,sort asc,time desc')
        ->paginate();
        
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
        
        return $this->fetch();
    }
    
    
    /**
     * 银行添加
     * @adminMenu(
     *     'name'   => '银行添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
       
        return $this->fetch();
    }
    /**
     * 银行添加do
     * @adminMenu(
     *     'name'   => '银行添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 银行详情
     * @adminMenu(
     *     'name'   => '银行详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
      
        return $this->fetch();
    }
    /**
     * 银行状态审核
     * @adminMenu(
     *     'name'   => '银行状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 银行状态批量同意
     * @adminMenu(
     *     'name'   => '银行状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 银行禁用
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
     * 银行信息状态恢复
     * @adminMenu(
     *     'name'   => '银行信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 银行编辑提交
     * @adminMenu(
     *     'name'   => '银行编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 银行编辑列表
     * @adminMenu(
     *     'name'   => '银行编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 银行审核详情
     * @adminMenu(
     *     'name'   => '银行审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 银行信息编辑审核
     * @adminMenu(
     *     'name'   => '银行编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 银行编辑记录批量删除
     * @adminMenu(
     *     'name'   => '银行编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 银行批量删除
     * @adminMenu(
     *     'name'   => '银行批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '银行批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        
        parent::del_all();
        
    }
    
    public function cates($type=3){
        parent::cates($type);
       
        $pic_conf=config('pic_bank');
        $this->assign('pic_conf',$pic_conf); 
        $this->assign('is_shows',[1=>'支持',2=>'不支持']); 
        
    }
    /*删除前检查  */
    public function del_before($ids){
        $m=$this->m;
        
        //检查是否有产品
        $where=['bank'=>['in',$ids]];
        $tmp=Db::name('paytype')->where($where)->find();
        if(!empty($tmp)){
            return ('银行'.$tmp['bank'].'下有收款账号'.$tmp['name']);
        }
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

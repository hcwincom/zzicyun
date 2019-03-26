<?php
 
namespace app\express\controller;

 
use app\common\controller\AdminInfoController; 
use think\Db; 
use app\express\model\AreaModel;
  
class AdminAreaController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='地区';
        $this->table='area';
        $this->m=new AreaModel();
      
        $this->base=['name'=>'str','fid'=>'int','type'=>'int','sort'=>'int','dsc'=>'str','code'=>'str','postcode'=>'str'];
        
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'name'=>'多语言名称', 
        ];
        
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
    }
    /**
     * 地区列表
     * @adminMenu(
     *     'name'   => '地区列表',
     *     'parent' => 'express/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 11,
     *     'icon'   => '',
     *     'remark' => '地区列表',
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
        
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        
        //上级地区 
        if(empty($data['country'])){
            $data['country']=0;
        }else{
            if(empty($data['province'])){
                $data['province']=0;
                $where_fid=['fid'=>$data['country']];
                $fids=$m->where($where_fid)->column('id');
                $fids[]=$data['country'];
                $where_fid=['fid'=>['in',$fids]];
                $fids1=$m->where($where_fid)->column('id');
                $fids=array_merge($fids,$fids1);
                $where['fid']=['in',$fids];
            }else{
                if(empty($data['city'])){
                    $where_fid=['fid'=>$data['province']];
                    $fids=$m->where($where_fid)->column('id');
                    $fids[]=$data['province'];
                    $where['fid']=['in',$fids];
                }else{
                    $where['fid']=['eq',$data['city']];
                }
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
        ->order('status asc,type asc,sort asc,time desc')
        ->paginate();
        
        // 获取分页显示
        $page = $list->appends($data)->render();
        //得到父级名称
        $fids=[];
        foreach($list as $v){
            $fids[]=$v['fid'];
        }
        $fnames=[];
        if(!empty($fids)){
            $fnames=$m->where('id','in',$fids)->column('id,name');
        }
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('fnames',$fnames);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
         
        return $this->fetch();
    }
     
   
    /**
     * 地区添加
     * @adminMenu(
     *     'name'   => '地区添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();  
        
    }
    /**
     * 地区添加do
     * @adminMenu(
     *     'name'   => '地区添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 地区详情
     * @adminMenu(
     *     'name'   => '地区详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();  
        return $this->fetch();  
    }
    /**
     * 地区状态审核
     * @adminMenu(
     *     'name'   => '地区状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 地区状态批量同意
     * @adminMenu(
     *     'name'   => '地区状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 地区禁用
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
     * 地区信息状态恢复
     * @adminMenu(
     *     'name'   => '地区信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 地区编辑提交
     * @adminMenu(
     *     'name'   => '地区编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 地区编辑列表
     * @adminMenu(
     *     'name'   => '地区编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 地区审核详情
     * @adminMenu(
     *     'name'   => '地区审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();  
    }
    /**
     * 地区信息编辑审核
     * @adminMenu(
     *     'name'   => '地区编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 地区编辑记录批量删除
     * @adminMenu(
     *     'name'   => '地区编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 地区批量删除
     * @adminMenu(
     *     'name'   => '地区批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '地区批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    { 
        parent::del_all();
    }
    /* 删除前 */
    public function del_before($ids){
        $m=$this->m;
        $tmp=$m->where(['fid'=>['in',$ids]])->find(); 
        if(!empty($tmp)){
            return '地区'.$tmp['fid'].'有下级地区'.$tmp['id'].'-'.$tmp['name'].',不能删除';
        }
        return 1;
    }
    public function cates($type=3){
        parent::cates($type);
        $this->assign('area_types',[0=>'国',1=>'省',2=>'市',3=>'县']);
    }
    public function param_check(&$data){
        if(empty($data['name'])){
            return '不能没有名称';
        }
        if(empty($data['country'])){
            $data['fid']=0;
            $data['type']=0;
        }else{
            if(empty($data['province'])){
                $data['fid']=$data['country'];
                $data['type']=1;
            }else{
                if(empty($data['city'])){
                    $data['fid']=$data['province'];
                    $data['type']=2;
                }else{
                    $data['fid']=$data['city'];
                    $data['type']=3;
                }
            }
            
        }
        
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        $m=$this->m;
        $info=$m->where('id',$data['id'])->find();
        if($info['type'] != $data['type']){
            return '地区级别不能改变';
        }
        return 1;
    }
    /* 查看详情 */
    public function edit_after($info){
        $m=$this->m;
        $data=$m->get_fids_by_id($info['id']);
        $this->assign('data',$data); 
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
        $m=$this->m;
        $data=$m->get_fids_by_id($info['id']);
        $this->assign('data',$data);
        if(isset($change['fid'])){
            $data_change=$m->get_fids_by_info($change['fid'],$info['type']);
            $this->assign('data_change',$data_change);
        }
       
       
    }
     
}

<?php
 
namespace app\express\controller;

 
use app\common\controller\AdminInfoController; 
use think\Db; 
use app\express\model\AreaModel; 
use app\express\model\StationModel;
  
class AdminStationController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='自提点';
        $this->table='station';
        $this->m=new StationModel();
      
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','type'=>'int','tel'=>'str',
            'province'=>'int','city'=>'int','area'=>'int','address_info'=>'str','work_time'=>'str'
        ];
        
        $this->isshop=0;
        $this->islan=0;
        
        
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
    }
    /**
     * 自提点列表
     * @adminMenu(
     *     'name'   => '自提点列表',
     *     'parent' => 'express/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 11,
     *     'icon'   => '',
     *     'remark' => '自提点列表',
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
        
        //自提点 地址 
        if(empty($data['province'])){
            $data['province']=0; 
        }else{
            $where['province']=['eq',$data['province']]; 
        }
        if(empty($data['city'])){
            $data['city']=0;
        }else{
            $where['city']=['eq',$data['city']];
        }
        if(empty($data['area'])){
            $data['area']=0;
        }else{
            $where['area']=['eq',$data['area']];
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
     * 自提点添加
     * @adminMenu(
     *     'name'   => '自提点添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();  
        
    }
    /**
     * 自提点添加do
     * @adminMenu(
     *     'name'   => '自提点添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 自提点详情
     * @adminMenu(
     *     'name'   => '自提点详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();  
        return $this->fetch();  
    }
    /**
     * 自提点状态审核
     * @adminMenu(
     *     'name'   => '自提点状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 自提点状态批量同意
     * @adminMenu(
     *     'name'   => '自提点状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 自提点禁用
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
     * 自提点信息状态恢复
     * @adminMenu(
     *     'name'   => '自提点信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 自提点编辑提交
     * @adminMenu(
     *     'name'   => '自提点编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 自提点编辑列表
     * @adminMenu(
     *     'name'   => '自提点编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 自提点审核详情
     * @adminMenu(
     *     'name'   => '自提点审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();  
    }
    /**
     * 自提点信息编辑审核
     * @adminMenu(
     *     'name'   => '自提点编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 自提点编辑记录批量删除
     * @adminMenu(
     *     'name'   => '自提点编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 自提点批量删除
     * @adminMenu(
     *     'name'   => '自提点批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '自提点批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    { 
        parent::del_all();
    }
    
    public function cates($type=3){
        parent::cates($type);
        $this->assign('station_types',[1=>'大陆',2=>'香港']);
    }
    public function param_check(&$data){
        
        if(empty($data['name'])){
            return '不能没有名称';
        }
        if(empty($data['area'])){
            return '地址没有填写完整';
        } 
        $m_area=new AreaModel();
        $data['address_info']=$m_area->get_addressinfo($data);
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        //地区改变
        $m=$this->m;
        $info=$m->where('id',$data['id'])->find();
       
        if($data['address_info']!=$info['address_info']){
            $content['address_info']=$data['address_info'];
            $content['area']=$data['area'];
            $content['city']=$data['city'];
            $content['province']=$data['province'];
        }
        return 1;
    }
    
     
}

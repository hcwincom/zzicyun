<?php
 
namespace app\shop\controller;

 
use app\common\controller\AdminInfoController; 
use think\Db; 
  
class AdminMyshopController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='店铺设置';
        $this->table='shop';
        $this->m=Db::name('shop');
        //没有店铺区分
        $this->isshop=0;
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','is_review'=>'int']; 
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        $this->assign('is_review',[1=>'二次审核',2=>'直接审核']);
    }
    
    /**
     * 我的店铺
     * @adminMenu(
     *     'name'   => '我的店铺',
     *     'parent' => 'shop/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '我的店铺',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $m=$this->m;
        $admin=$this->admin;
        $id=$admin['shop'];
       
        $info=$m
        ->alias('p')
        ->field('p.*,a.user_nickname as aname,r.user_nickname as rname')
        ->join('cmf_user a','a.id=p.aid','left')
        ->join('cmf_user r','r.id=p.rid','left')
        ->where('p.id',$id)
        ->find();
        if(empty($info)){
            $this->error('数据不存在');
        }
        $this->assign('info',$info);
        
        $this->where_shop=$id;
        
        //对应分类数据
        $this->cates(); 
        return $this->fetch();  
    }
    
    
    /**
     * 我的店铺编辑提交
     * @adminMenu(
     *     'name'   => '我的店铺编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '我的店铺编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 我的店铺编辑列表
     * @adminMenu(
     *     'name'   => '我的店铺编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '我的店铺编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        $table=$this->table;
        $m_edit=Db::name('edit');
        $flag=$this->flag;
        $data=$this->request->param();
        $admin=$this->admin;
        //查找当前表的编辑
        $where=['e.table'=>['eq',$table]];
        $join=[
            ['cmf_'.$table.' p','e.pid=p.id','left'],
        ];
        
        $field='e.*,p.name as pname';
        //店铺
        
        $where['p.id']=['eq',$admin['shop']];
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['e.rstatus']=['eq',$data['status']];
        }
        //编辑人
        if(empty($data['aid'])){
            $data['aid']=0;
        }else{
            $where['e.aid']=['eq',$data['aid']];
        }
        //审核人
        if(empty($data['rid'])){
            $data['rid']=0;
        }else{
            $where['e.rid']=['eq',$data['rid']];
        }
        //所属分类
        if(empty($data['cid'])){
            $data['cid']=0;
        }else{
            $where['p.cid']=['eq',$data['cid']];
        }
        
        
        //时间类别
        $times=config('edit_time_search');
        zz_search_time($times,$data,$where,['alias'=>'e.']);
        
        $list=$m_edit
        ->alias('e')
        ->field($field)
        ->join($join)
        ->where($where)
        ->order('e.rstatus asc,e.atime desc')
        ->paginate();
        
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $this->cates(2);
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        
        $this->assign('data',$data); 
        $this->assign('times',$times);
       
        return $this->fetch();  
    }
    
    /**
     * 我的店铺审核详情
     * @adminMenu(
     *     'name'   => '我的店铺审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '我的店铺审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();  
    }
    /**
     * 我的店铺信息编辑审核
     * @adminMenu(
     *     'name'   => '我的店铺编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '我的店铺编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
     
     
}

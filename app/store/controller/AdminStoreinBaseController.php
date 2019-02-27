<?php
 
namespace app\store\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsModel;
 
/* 产品的添加 */
class AdminStoreinBaseController extends AdminInfoController
{
    private $about_type;
    public function _initialize()
    {
        parent::_initialize();
       
        
        $this->base=['name'=>'str','goods'=>'int','dsc'=>'str','type'=>'int','num'=>'int',
            'about'=>'int','about_name'=>'str'
        ];
         
        $this->isshop=1;
        $this->islan=0;
        $this->statuss=config('store_in_status');
       
        $this->assign('statuss',$this->statuss);
        
         
    }
    
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
            $where['p.status']=['eq',$data['status']];
        }
        
        //添加人
        if(empty($data['aid'])){
            $data['aid']=0;
        }else{
            $where['p.aid']=['eq',$data['aid']];
        }
        //审核人
        if(empty($data['rid'])){
            $data['rid']=0;
        }else{
            $where['p.rid']=['eq',$data['rid']];
        }
        
        //类型
        if(empty($data['type'])){
            $data['type']=0;
        }else{
            $where['p.type']=['eq',$data['type']];
        }
        //查询字段 
        $types=[2=>['p.name','名称'],1=>['p.id','id']];  
        $search_types=config('search_types');
        zz_search_param($types,$search_types,$data,$where);
        
        
        //时间类别
        $times=[
            1 =>
            array (
                0 => 'p.atime',
                1 => '创建时间',
            ),
            2 =>
            array (
                0 => 'p.rtime',
                1 => '审核时间',
            ),
            3 =>
            array (
                0 => 'p.time',
                1 => '更新时间',
            ),
        ];
        zz_search_time($times,$data,$where);
        $field='p.*,goods.name as goods_name,goods.code as goods_code,goods.store_code,goods.store_num';
        $list=$m
        ->field($field)
        ->alias('p')
        ->join('cmf_goods goods','goods.id=p.goods')
        ->where($where)
        ->order('p.status asc,p.id desc')
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
     
    
    public function add()
    {
        parent::add();
        
        $this->assign('prices',null);
        $this->assign('pdfs',null);
        
        return $this->fetch();
    }
    /**
     * 产品添加do
     * @adminMenu(
     *     'name'   => '产品添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品详情
     * @adminMenu(
     *     'name'   => '产品详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品状态审核
     * @adminMenu(
     *     'name'   => '产品状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品状态批量同意
     * @adminMenu(
     *     'name'   => '产品状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    
     
    
    public function cates($type=3){
        parent::cates($type);
        $m_cate=new GoodsCateModel();
        $where=[
           'status'=>2, 
        ];
        $cates=$m_cate->all_cates1($where);
        $this->assign('cates',$cates);
         
    }
    /*删除前检查  */
    public function del_before($ids){
        
        
        return '不能删除';
    }
    /**
     * 参数值过滤
     *   */
    public function param_check(&$data){
        
        $admin=$this->admin;
        $table=$this->table;
        $data['num']=intval($data['num']);
       
        //产品和数量
        if(empty($data['goods']) || empty($data['num'])){
            return '产品和数量必须选择';
        } 
        $m_goods=new GoodsModel();
        //判断供应商
        $goods=$m_goods->where('id',$data['goods'])->find();
        $data['shop']=$goods['shop'];
        $data['name']=date('YmdHis').'_'.$admin['id'].'_'.$data['goods'];
        switch($data['type']){
            case 10:
                //订单出库和入库
                if($table=='store_in'){
                    $data['about']=$data['shop'];
                    $data['about_name']=Db::name('shop')->where('id',$data['about'])->value('name');
                }else{
                    if(empty($data['about'])){
                        $data['about']=0;
                        $data['about_name']='';
                    }else{
                        $data['about']=0;
                        $data['about_name']='';
                    }
                }
                
                break;
           
            case 30:
                //库存调整 
                $data['about']=$admin['id'];
                $data['about_name']=$admin['user_login'];
                
                break; 
                
        } 
         
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        
        $table=$this->table;
        $data_update=[];
        $m=$this->m;
        $m->where('id',$id)->update($data_update); 
        
        
        return 1;
    }
    
    /* 查看详情 */
    public function edit_after($info){
      
        $m_cate=new GoodsCateModel();
        $cate_info=$m_cate->cate_info(1);
       
    }
    public function review_before($info,$status){
        return 1;
    }
   
    
}

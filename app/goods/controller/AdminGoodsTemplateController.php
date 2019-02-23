<?php
 
namespace app\goods\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsParamModel;
/* 产品模板的添加 */
class AdminGoodsTemplateController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='产品模板';
        $this->table='goods_template';
        $this->m=DB::name('goods_template');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','cid'=>'int','rate'=>'int'];
         
        $this->isshop=0;
        $this->islan=0;
        
        $this->assign('flag','产品模板');
         
    }
    /**
     * 产品模板列表
     * @adminMenu(
     *     'name'   => '产品模板列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 30,
     *     'icon'   => '',
     *     'remark' => '产品模板列表',
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
     * 产品模板树
     * @adminMenu(
     *     'name'   => '产品模板树',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板树',
     *     'param'  => ''
     * )
     */
    public function tree()
    { 
       
        $m=$this->m;
        $list=$m->all_cates(1,1,'p.*,val.val',[]);
        
        $this->assign('list',$list); 
        
        return $this->fetch();
    }
   
    /**
     * 产品模板添加
     * @adminMenu(
     *     'name'   => '产品模板添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        $fid=$this->request->param('fid');
        $this->assign('fid',$fid);
        
        return $this->fetch();
    }
    /**
     * 产品模板添加do
     * @adminMenu(
     *     'name'   => '产品模板添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品模板详情
     * @adminMenu(
     *     'name'   => '产品模板详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品模板状态审核
     * @adminMenu(
     *     'name'   => '产品模板状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品模板状态批量同意
     * @adminMenu(
     *     'name'   => '产品模板状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 产品模板禁用
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
     * 产品模板信息状态恢复
     * @adminMenu(
     *     'name'   => '产品模板信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 产品模板编辑提交
     * @adminMenu(
     *     'name'   => '产品模板编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 产品模板编辑列表
     * @adminMenu(
     *     'name'   => '产品模板编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 产品模板审核详情
     * @adminMenu(
     *     'name'   => '产品模板审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 产品模板信息编辑审核
     * @adminMenu(
     *     'name'   => '产品模板编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 产品模板编辑记录批量删除
     * @adminMenu(
     *     'name'   => '产品模板编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 产品模板批量删除
     * @adminMenu(
     *     'name'   => '产品模板批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品模板批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
         
        parent::del_all();
        
    }
    
    public function cates($type=3){
        parent::cates($type);
        $m=new GoodsCateModel();
        $where=[
           'status'=>2,
           'rate'=>['lt',3],
        ];
        $cates=$m->all_cates1($where);
        $this->assign('cates',$cates);
        $this->assign('template_rates',[1=>'一级分类模板',2=>'二级分类模板']);
        //获取所有参数用于选择
        $m_param=new GoodsParamModel();
        $params=$m_param->get_all();
        $this->assign('params',$params);
        $this->assign('params_ids',[]);
    }
    
    /**
     * 参数值过滤
     *   */
    public function param_check(&$data){
        $res=parent::param_check($data);
        if(!($res>0)){
            return $res;
        }
        //得到级别
        if(empty($data['cid2'])){
            if(empty($data['cid1'])){
                return '所属分类必须选择';
            }else{
                $data['cid']=$data['cid1'];
                $data['rate']=1;
            }
        }else{
            $data['cid']=$data['cid2']; 
            $data['rate']=2;
        }
        
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
       if(empty($data['ids'])){
           return 1;
       }
       $data_add=[];
       foreach($data['ids'] as $k=>$v){
           $data_add[]=[
               'template'=>$id,
               'param'=>$k,
               'sort'=>$data['sorts'][$k],
               'is_search'=>$data['is_searchs'][$k],
              
           ];
       }
       $m_tp=Db::name('goods_template_param');
       $m_tp->insertAll($data_add);
       return 1;
    }
    /* 查看详情 */
    public function edit_after($info){
        $cid1=0;
        $cid2=0;          
        switch($info['rate']){
            case 1:
                $cid1=$info['cid'];
                break;
            case 2: 
                $cid2=$info['cid'];
                $m=new GoodsCateModel();
                $cid1=$m->where('id',$cid2)->value('fid');
                break;
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2); 
        $m_tp=Db::name('goods_template_param');
        $params_ids=$m_tp->where('template',$info['id'])->order('sort asc')->column('param,sort,is_search');
         
        $this->assign('params_ids',$params_ids); 
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
      
        $m_tp=Db::name('goods_template_param');
        $params_ids=$m_tp->where('template',$data['id'])->order('sort asc')->column('param,sort,is_search');
        
        //都不存在，无改变
        if(empty($data['ids']) && empty($params_ids)){
            return 1;
        }
        $data_add=[];
        if(empty($data['ids']) ){
            //都不存在，无改变
            if(empty($params_ids)){
                return 1;
            }else{
                //全部删除
                foreach($params_ids as $k=>$v){
                    $content['param']['del'][$k]=$k;
                }
                return 1;
            }
        }else{
            //全部新增
            if(empty($params_ids)){
                foreach($data['ids'] as $k=>$v){
                    $content['param']['add'][$k]=[
                        'template'=>$data['id'],
                        'param'=>$k,
                        'sort'=>$data['sorts'][$k],
                        'is_search'=>$data['is_searchs'][$k],
                        
                    ];
                }
                return 1;
            }
        }
        //有新增有删除
        foreach($data['ids'] as $k=>$v){
            //新增
            if(empty($params_ids[$k])){
                $content['param']['add'][$k]=[
                    'template'=>$data['id'],
                    'param'=>$k,
                    'sort'=>$data['sorts'][$k],
                    'is_search'=>$data['is_searchs'][$k],
                    
                ];
            }else{
                //比较变化
                if($params_ids[$k]['sort']!=$data['sorts'][$k]){
                    $content['param']['edit'][$k]['sort']=$data['sorts'][$k];
                }
                if($params_ids[$k]['is_search']!=$data['is_searchs'][$k]){
                    $content['param']['edit'][$k]['is_search']=$data['is_searchs'][$k];
                }
            }
        }
        //删除
        foreach($params_ids as $k=>$v){
            //新增
            if(empty($data['ids'][$k])){
                $content['param']['del'][$k]=$k;
            }  
        }
        return 1;
    }
    /* 编辑审核前的操作 */
    public function edit_review_before($info1){
        //得到修改的字段
        $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
        $change=json_decode($change,true);
        $m_tp=Db::name('goods_template_param');
        //先删除
        if(isset($change['param']['del'])){
            $where_del=[
                'template'=>$info1['pid'],
                'param'=>['in',$change['param']['del']]
            ];
            $m_tp->where($where_del)->delete();
        }
        //修改
        if(isset($change['param']['edit'])){
            foreach($change['param']['edit'] as $k=>$v){
                $where_edit=[
                    'template'=>$info1['pid'],
                    'param'=>$k
                ];
                $m_tp->where($where_edit)->update($v);
            }
        }
        //新增
        if(isset($change['param']['add'])){
            //新删除再新增
            $ids=array_keys($change['param']['add']);
            $where_del=[
                'template'=>$info1['pid'],
                'param'=>['in',$ids]
            ];
            $m_tp->where($where_del)->delete();
            $m_tp->insertAll($change['param']['add']); 
        }
        if(isset($change['param'])){
            unset($change['param']);
        }
        return $change;
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
        $cid1=0;
        $cid2=0;
        $m=new GoodsCateModel();
        switch($info['rate']){
            case 1:
                $cid1=$info['cid'];
                break;
            case 2:
                $cid2=$info['cid']; 
                $cid1=$m->where('id',$cid2)->value('fid');
                break;
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2);
        if(isset($change['cid'])){
            $cname='';
            switch($info['rate']){ 
                case 1:
                    $cname=$m->where('id',$change['cid'])->value('name');
                    break;
                case 2:
                    $cname=$m
                    ->alias('c2')
                    ->join('cmf_goods_cate c1','c1.id=c2.fid')
                    ->where('c2.id',$change['cid'])
                    ->value('concat(c1.name,"-",c2.name)');
            }
            $this->assign('cname',$cname);
        }
        $m_tp=Db::name('goods_template_param');
        $params_ids=$m_tp->where('template',$info['id'])->order('sort asc')->column('param,sort,is_search');
         
        $this->assign('params_ids',$params_ids); 
    }
   
}

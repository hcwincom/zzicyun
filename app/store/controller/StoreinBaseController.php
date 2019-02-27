<?php
 
namespace app\store\controller;
 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsModel;
 
/* 出入库的添加 */
class StoreinBaseController extends AdminInfoController
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
         
        return $this->fetch();
    }
    /**
     * 出入库添加 
     */
    public function add_do()
    {
        $m=$this->m;
        $data=$this->request->param();
        $res=$this->param_check($data);
        
        if(!($res>0)){
            $this->error($res);
        }
        
        $url=url('index');
        
        $table=$this->table;
        $time=time();
        $admin=$this->admin;
        $fields=$this->base;
        //str,int,round1,round2,round3,round4,pic,file
        $data_add=[];
        //添加的信息
        
        foreach($fields as $k=>$v){
            if(isset($data[$k])){
                $data_add[$k]=$data[$k];
            }
        }
         
        $m->startTrans();
        $id=$m->instore_add($data_add,$admin); 
        if(!($id>0)){
            $this->error($id);
        }
        
        //记录操作记录
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'添加'.($this->flag).$id,
            'table'=>($this->table),
            'type'=>'add',
            'pid'=>$id,
            'link'=>url('edit',['id'=>$id]),
            'shop'=>$admin['shop'],
            
        ];
        zz_action($data_action,$admin);
        
        $m->commit();
        //直接审核
        $rule='review';
        $res=$this->check_review($admin,$rule);
        if($res){
            $this->redirect($rule,['id'=>$id,'status'=>2]);
        }
        $this->success('添加成功',$url);
    }
    
    /**
     * 出入库详情 
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 出入库状态审核 
     */
    public function review()
    {
        $status=$this->request->param('status',0,'intval');
        $id=$this->request->param('id',0,'intval');
        $rdsc=$this->request->param('rdsc','');
        if($status<2 || $status>3 || $id<=0){
            $this->error('信息错误');
        }
        $m=$this->m;
        //查找信息
        $info=$m->where('id',$id)->find();
        if(empty($info)){
            $this->error('信息不存在');
        }
        $admin=$this->admin;
        //其他店铺的审核判断
        if($admin['shop']!=1){
            if(empty($info['shop']) || $info['shop']!=$admin['shop']){
                $this->error('不能审核其他店铺的信息');
            }
        }
        
        $time=time();
        if(empty($rdsc)){
            $statuss=$this->statuss;
            $rdsc=$statuss[$status];
        }
        $m->startTrans();
        $row=$m->instore_review($info,$admin,$status,$rdsc);
        
        if($row!==1){
            $m->rollback();
            $this->error('审核失败，请刷新后重试');
        }
         
        //审核后的操作
        $res=$this->review_after($info,$status);
        if(!($res>0)){
            $m->rollback();
            $this->error($res);
        }
        $m->commit();
        $this->success('审核成功');
    }
    /**
     * 出入库状态还原
     */
    public function review_back()
    {
       
        $id=$this->request->param('id',0,'intval');
        $rdsc=$this->request->param('rdsc','');
        
        $m=$this->m;
        //查找信息
        $info=$m->where('id',$id)->find();
        if(empty($info) || $info['status']<2 || $info['status']>3){
            $this->error('没有可还原的信息');
        }
        $admin=$this->admin;
        //其他店铺的审核判断
        if($admin['shop']!=1){
            if(empty($info['shop']) || $info['shop']!=$admin['shop']){
                $this->error('不能审核其他店铺的信息');
            }
        }
        
        $time=time();
        if(empty($rdsc)){ 
            $rdsc='还原出入库状态';
        }
        $m->startTrans();
        $row=$m->instore_back($info,$admin,$rdsc);
        
        if($row!==1){
            $m->rollback();
            $this->error('审核失败，请刷新后重试');
        }
        
        //审核后的操作
        $res=$this->review_after($info,1);
        if(!($res>0)){
            $m->rollback();
            $this->error($res);
        }
        $m->commit();
        $this->success('审核成功');
    }
    /**
     * 出入库状态批量审核
     */
    public function review_all()
    {
        if(empty($_POST['ids'])){
            $this->error('未选中信息');
        }
        $ids=$_POST['ids'];
        $m=$this->m;
        $admin=$this->admin;
        $time=time();
        $where=[
            'id'=>['in',$ids],
            'status'=>['eq',1],
        ];
        //其他店铺检查,如果没有shop属性就只能是1号主站操作,有shop属性就带上查询条件
        if($admin['shop']!=1){
            if($this->isshop){
                $where['shop']=['eq',$admin['shop']];
            }else{
                $this->error('店铺操作系统数据');
            }
        }
        $status=$this->request->param('status',0,'intval');
        if($status<2 || $status>3){
            $this->error('状态选择错误');
        }
         $statuss=$this->statuss;
         $rdsc='批量'.$statuss[$status];
        //得到要更改的数据
        $list=$m->where($where)->column('');
        if(empty($list)){
            $this->error('没有可以批量审核的数据');
        }
        $m->startTrans();
        foreach($list as $k=>$v){
            $row=$m->instore_review($v,$admin,$status,$rdsc);
            
            if($row!==1){
                $m->rollback();
                $this->error('审核失败，请刷新后重试');
            }
        }
        //记录ids
        $ids=array_keys($list);
        $ids=implode(',',$ids);
        
        //审核成功，记录操作记录,发送审核信息
        $flag=$this->flag;
        
        $table=$this->table;
        //记录操作记录
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].$rdsc.$flag.'('.$ids.')',
            'table'=>$table,
            'type'=>'review_all',
            'link'=>'',
            'shop'=>$admin['shop'],
        ];
      
        
        zz_action($data_action,['pids'=>$ids]);
       
        $m->commit();
        $this->success('审核成功'.count($list).'条数据');
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
       
        //出入库和数量
        if(empty($data['goods']) || empty($data['num'])){
            return '产品和数量必须选择';
        } 
          
        return 1;
    }
    
    
    /* 查看详情 */
    public function edit_after($info){
        $m_goods=new GoodsModel();
        $goods=$m_goods->where('id',$info['goods'])->find();
        $m_cate=new GoodsCateModel();
        $cate_info=$m_cate->cate_info($goods['cid']);
        $this->assign('goods_info',$goods);
        $this->assign('cate_info',$cate_info);
    }
     
    
}

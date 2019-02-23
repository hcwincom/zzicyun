<?php
 
namespace app\common\controller;

use cmf\controller\AdminBaseController; 
use think\Db; 
/*
 * 数据的操作基类
 */
class AdminInfo0Controller extends AdminBaseController
{
    protected $m;
    protected $statuss;
    protected $review_status;
    protected $table;
    protected $fields;
    protected $flag;
    protected $isshop;
    protected $islan; 
    //用于详情页中识别当前店铺, 
    //列表页中分店铺查询
   
    protected $base;
    protected $add;
    protected $edit;
    protected $search;
    /**
     * 不同语言区分
     * @var array
     */
    protected $vals;
   
    public function _initialize()
    {
        parent::_initialize();
        
        $this->statuss=config('info_status');
        $this->review_status=config('review_status');
       
        //str,int,round1,round2,round3,round4,pic,file
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','cid'=>'int'];
        
        $this->search=[2=>['name','名称'],1=>['id','id']];  
        $this->assign('statuss',$this->statuss);
        $this->assign('review_status',$this->review_status);
       
        $this->assign('html',$this->request->action());
        
    }
    /**
     *首页
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
        if(empty($data['cid'])){
            $data['cid']=0;
        }else{
            $where['cid']=['eq',$data['cid']];
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
       
    } 
    /**
     * 信息添加
     */
    public function add()
    {
        $admin=$this->admin;
       
        if($this->isshop){
            $this->where_shop=($admin['shop']==1)?2:$admin['shop'];
        } 
        
        $this->cates();
        $this->assign("info", null);
        if($this->islan){
            $this->assign("lans_info", null);
        }
        
    }
    /* 添加 */
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
      
        //判断是否有店铺
        if($this->isshop){
            $data_add['shop']=$admin['shop'];
        } 
      
        $data_add['status']=1; 
        $data_add['aid']=$admin['id'];
        $data_add['atime']=$time;
        $data_add['time']=$time;
      
        $m->startTrans();
        $id=$m->insertGetId($data_add);
        $res=$this->add_do_after($id,$data);
        if(!($res>0)){
            $this->error($res);
        }
        //多语言
        if($this->islan){
            $vals=$this->vals;
            $data_vals=[];
            foreach($vals as $k=>$v){
                foreach($data['lan-'.$k] as $kk=>$vv){
                    if(isset($data_vals[$k.'-'.$kk])){
                        $data_vals[$k.'-'.$kk][$k]=$vv;
                    }else{
                        $data_vals[$k.'-'.$kk]=[
                            'pid'=>$id,
                            'lid'=>$kk,
                            $k=>$vv,
                        ];
                    } 
                } 
            }
            Db::name($table.'_val')->insertAll($data_vals);
        }
        //记录操作记录 
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'添加'.($this->flag).$id.'-'.$data['name'],
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
     * 信息详情 
     */ 
    public function edit()
    {
        $m=$this->m;
        $id=$this->request->param('id',0,'intval');
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
        if($this->isshop){
            $admin=$this->admin;
            if($admin['shop']!=1 && $admin['shop']!=$info['shop']){
                $this->error('只能查看自己店铺的编辑信息');
            }
            $this->where_shop=$info['shop'];
        }
        //对应分类数据
        $this->cates(); 
        if($this->islan){
            $table=$this->table;
            $lans_info=Db::name($table.'_val')->where('pid',$id)->column('*','lid');
            $this->assign("lans_info", $lans_info); 
        }
        $this->edit_after($info);
    }
    
    //信息审核
    public function review()
    {
        $status=$this->request->param('status',0,'intval');
        $id=$this->request->param('id',0,'intval');
        if($status<1 || $status>4 || $id<=0){
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
        $update=[
            'rid'=>$admin['id'],
            'rtime'=>$time,
            'status'=>$status,
            'time'=>$time,
        ];
        
        $row=$m->where('id',$id)->update($update);
         
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
     * 信息状态批量同意 
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
        
        $update=[
            'status'=>2,
            'time'=>$time,
            'rid'=>$admin['id'],
            'rtime'=>$time,
        ];
        //得到要更改的数据
        $list=$m->where($where)->column('id');
        if(empty($list)){
            $this->error('没有可以批量审核的数据');
        }
       
        $ids=implode(',',$list);
        
        //审核成功，记录操作记录,发送审核信息
        $flag=$this->flag;
       
        $table=$this->table;
        //记录操作记录  
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'批量同意'.$flag.'('.$ids.')',
            'table'=>$table,
            'type'=>'review_all',
            'link'=>'',
            'shop'=>$admin['shop'],
        ];
        $m->startTrans();
        
        zz_action($data_action,['pids'=>$ids]);
        $rows=$m->where('id','in',$list)->update($update);
        if($rows<=0){
            $m->rollback();
            $this->error('没有数据审核成功，批量审核只能把未审核的数据审核为正常');
        } 
        
        $m->commit();
        $this->success('审核成功'.$rows.'条数据');
    }
    
    /**
     * 信息状态禁用 
     */
    public function ban()
    {
        //区分是一个还是数组
        $id=$this->request->param('id',0,'intval');
       
        $where=['status'=>['eq',2]];
        if($id>0){
            $where['id']=['eq',$id];
        }elseif(empty($_POST['ids'])){
            $this->error('未选中信息');
        }else{
            $ids=$_POST['ids'];
            $where['id']=['in',$ids];
        }
        //其他店铺检查,如果没有shop属性就只能是1号主站操作,有shop属性就带上查询条件
        $admin=$this->admin;
        if($admin['shop']!=1){
            if($this->isshop){
                $where['shop']=['eq',$admin['shop']];
            }else{
                $this->error('店铺不能操作系统数据');
            }
        }
        $m=$this->m;
       
        $update=['status'=>4];
        $rows=$m->where($where)->update($update);
         
        if($rows>=1){
             
            $this->success('已禁用'.$rows.'条数据');
        }else{
            $this->error('没有成功禁用数据，禁用是指将状态为正常改为禁用');
        }
    }
    /**
     * 信息状态恢复 
     */
    public function cancel_ban()
    {
        
        //区分是一个还是数组
        $id=$this->request->param('id',0,'intval');
        
        $where=['status'=>['eq',4]];
        if($id>0){
            $where['id']=['eq',$id];
        }elseif(empty($_POST['ids'])){
            $this->error('未选中信息');
        }else{
            $ids=$_POST['ids'];
            $where['id']=['in',$ids];
        }
        //其他店铺检查,如果没有shop属性就只能是1号主站操作,有shop属性就带上查询条件
        $admin=$this->admin;
        if($admin['shop']!=1){
            if($this->isshop){
                $where['shop']=['eq',$admin['shop']];
            }else{
                $this->error('店铺操作系统数据');
            }
        }
        $m=$this->m; 
        $update=['status'=>2];
        $rows=$m->where($where)->update($update);
        
        if($rows>=1){
            $this->success('已恢复'.$rows.'条数据');
        }else{
            $this->error('没有成功恢复数据,恢复是指将状态为禁用改为正常');
        }
    }
     
    /**
     * 编辑提交 
     */
    public function edit_do(){
         
        $m=$this->m;
        $table=$this->table;
        $flag=$this->flag;
        $data=$this->request->param();
        $res=$this->param_check($data);
        if($res!=1){
            $this->error($res);
        }
        $info=$m->where('id',$data['id'])->find();
        if(empty($info)){
            $this->error('数据不存在');
        }
        $time=time();
        $admin=$this->admin;
        //其他店铺的审核判断 
        if($admin['shop']!=1){
            if(empty($info['shop']) || $info['shop']!=$admin['shop']){
                $this->error('不能编辑其他店铺的信息');
            }
        }
        $update=[
            'pid'=>$info['id'],
            'aid'=>$admin['id'],
            'atime'=>$time, 
            'table'=>$table,
            'url'=>url('edit_info','',false,false),
            'rstatus'=>1,
            'rid'=>0,
            'rtime'=>0,
            'shop'=>$admin['shop'],
        ];
        $update['adsc']=(empty($data['adsc']))?('修改了'.$flag.'信息'):$data['adsc'];
        $fields=$this->base;
        //str,int,round1,round2,round3,round4,pic,file
        $content=[];
        //检测改变了哪些字段
        //如果原信息和$data信息相同就未改变，不为空就记录，？null测试 
        foreach($fields as $k=>$v){ 
            if(isset($data[$k])){ 
                if($info[$k]!=$data[$k]){
                    $content[$k]=$data[$k];
                } 
            } 
        }
       
        //多语言
        if($this->islan){
            //获取原多语言值
            $lans_info=Db::name($table.'_val')->where('pid',$info['id'])->column('*','lid');
            $vals=$this->vals;
            $data_vals=[];
            foreach($vals as $k=>$v){
                foreach($data['lan-'.$k] as $kk=>$vv){
                    if(!isset($lans_info[$kk][$k]) || $lans_info[$kk][$k] != $vv){
                        $data_vals[$kk][$k]=$vv; 
                    } 
                }
            } 
            if(!empty($data_vals)){
                $content['lans']=$data_vals;
            }
           
        }
        if(empty($content)){
            $this->error('未修改');
        }
        //保存更改 
        $m_edit=Db::name('edit');
        $m_edit->startTrans();
        $eid=$m_edit->insertGetId($update);
        if($eid>0){
            $data_content=[
                'eid'=>$eid,
                'content'=>json_encode($content),
            ];
            Db::name('edit_info')->insert($data_content);
        }else{
            $m_edit->rollback();
            $this->error('保存数据错误，请重试');
        }
        
        //记录操作记录
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'编辑了'.($this->flag).$info['id'].'-'.$info['name'],
            'table'=>($this->table),
            'type'=>'edit',
            'pid'=>$info['id'],
            'link'=>url('edit_info',['id'=>$eid]),
            'shop'=>$admin['shop'], 
        ];
      
        zz_action($data_action,['department'=>$admin['department']]);
        
        $m_edit->commit();
        //判断是否直接审核
        $rule='edit_review';
        $res=$this->check_review($admin,$rule);
        if($res){
            $this->redirect($rule,['id'=>$eid,'rstatus'=>2,'rdsc'=>'直接审核']);
        }
        
        $this->success('已提交修改');
    }
    
    /* 批量删除 */
    public function del_all()
    {
        if(empty($_POST['ids'])){
            $this->error('未选中信息');
        }
        $ids=$_POST['ids'];
        
        $m=$this->m;
        $flag=$this->flag;
        $table=$this->table;
        $admin=$this->admin;
        $time=time();
        //彻底删除
        $where=['id'=>['in',$ids]];
        //其他店铺检查,如果没有shop属性就只能是1号主站操作,有shop属性就带上查询条件 
        if($admin['shop']>1){ 
            if($this->isshop){
                $where['shop']=['eq',$admin['shop']];
                $ids=$m->where($where)->column('id');
                if(empty($ids)){
                    $this->error('没有可删除数据');
                }
            }else{
                $this->error('店铺不能操作系统数据');
            }
        } 
        $res=$this->del_before($ids);
        if(!($res>0)){
            $this->error($res);
        }
        $m->startTrans();
        $tmp=$m->where('id','in',$ids)->delete(); 
        //删除多语言值
        if($this->islan){
            Db::name($table.'_val')->where('pid','in',$ids)->delete();
        }
         
        //删除关联编辑记录
        $where_edit=[
            'table'=>['eq',$table],
            'pid'=>['in',$ids],
        ];
        //现获取编辑id来删除info
        $eids=Db::name('edit')->where($where_edit)->column('id');
        if(!empty($eids)){
            Db::name('edit_info')->where(['eid'=>['in',$eids]])->delete();
            Db::name('edit')->where(['id'=>['in',$eids]])->delete();
        }
        
        //记录操作记录
        $idss=implode(',',$ids);
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'批量删除'.$flag.'('.$idss.')',
            'table'=>$table,
            'type'=>'del',
            'link'=>'',
            'pid'=>0,
            'shop'=>$admin['shop'],
        ];
       
        zz_action($data_action,['pids'=>$idss]);
        $m->commit(); 
        $this->success('成功删除数据'.$tmp.'条');
       
    }
    /**
     * 编辑列表
     */
    public function edit_list()
    {
        
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
        //店铺,分店只能看到自己的数据，总店可以选择店铺
        if($this->isshop){
            zz_shop($admin, $data, $where,'e.shop');
            if($admin['shop']>1 ){
                $where['p.shop']=['eq',$admin['shop']];
            }
        }
       
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
        
        //查询字段
        $types=$this->search;
        $search_types=config('search_types');
        zz_search_param($types,$search_types,$data,$where,['alias'=>'p.']);
        
        
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
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
    }
    /**
     * 编辑审核详情
     */
    public function edit_info()
    {
        $m=$this->m;
        $id=$this->request->param('id',0,'intval');
        $table=$this->table;
        //获取编辑信息
        $m_edit=Db::name('edit');
        $info1=$m_edit
        ->alias('p')
        ->field('p.*,a.user_nickname as aname,r.user_nickname as rname')
        ->join('cmf_user a','a.id=p.aid','left')
        ->join('cmf_user r','r.id=p.rid','left')
        ->where('p.id',$id)
        ->find();
        
        if(empty($info1)){
            $this->error('编辑信息不存在');
        }
        $admin=$this->admin;
        if($admin['shop']!=1 && $admin['shop']!=$info1['shop']){
            $this->error('只能查看自己店铺的编辑信息');
        }
        //获取原信息
        $info=$m
        ->alias('p')
        ->field('p.*,a.user_nickname as aname,r.user_nickname as rname')
        ->join('cmf_user a','a.id=p.aid','left')
        ->join('cmf_user r','r.id=p.rid','left')
        ->where('p.id',$info1['pid'])
        ->find();
        if(empty($info)){
            $this->error('编辑关联的信息不存在');
        }
        //获取改变的信息
        $change=Db::name('edit_info')->where('eid',$id)->value('content');
        $change=json_decode($change,true);
        
        
        $this->assign('info',$info);
        $this->assign('info1',$info1);
        $this->assign('change',$change);
        if($this->islan){
            $lans_info=Db::name($table.'_val')->where('pid',$info1['pid'])->column('*','lid');
            $this->assign("lans_info", $lans_info);
        }
       
        //分类关联信息
        $this->cates();
        $this->edit_info_after($info,$change);
    }
    
    /**
     * 信息编辑审核
     */
    public function edit_review()
    {
        //审核编辑的信息
        $status=$this->request->param('rstatus',0,'intval');
        $id=$this->request->param('id',0,'intval');
        if(($status!=2 && $status!=3) || $id<=0){
            $this->error('信息错误');
        }
        $m=$this->m;
        $table=$this->table;
        $m_edit=Db::name('edit');
        $info1=$m_edit
        ->field('e.*,a.user_nickname as aname')
        ->alias('e') 
        ->join('cmf_user a','a.id=e.aid')
        ->where('e.id',$id)
        ->find();
        if(empty($info1)){
            $this->error('无效编辑信息');
        }
        $info=$m->where('id',$info1['pid'])->find();
        if(empty($info)){
            $this->error('编辑关联的信息不存在');
        }
        if($info1['rstatus']!=1){
            $this->error('编辑信息已被审核！不能重复审核');
        }
        
        $admin=$this->admin;
        //其他店铺的审核判断
        if($admin['shop']!=1 && $info1['shop']!=$admin['shop']){
            $this->error('不能审核其他店铺的信息');
        }
        
        $time=time();
        
        $m->startTrans();
        
        $update=[
            'rid'=>$admin['id'],
            'rtime'=>$time,
            'rstatus'=>$status,
        ];
        $review_status=$this->review_status;
        $update['rdsc']=$this->request->param('rdsc','');
        if(empty($update['rdsc'])){
            $update['rdsc']=$review_status[$status];
        }
        
        //只有未审核的才能更新
        $where=[
            'id'=>$id,
            'rstatus'=>1,
        ];
        $row=$m_edit->where($where)->update($update);
        if($row!==1){
            $m->rollback();
            $this->error('审核失败，请刷新后重试');
        }
        //是否更新,2同意，3不同意
        if($status==2){
           
            //组装更新数据
            $update_info=[
                'time'=>$time,
            ];
          
            //审核前的检查
            $change=$this->edit_review_before($info1);
            if(isset($change['change_error'])){
                $m->rollback();
                $this->error($change['change_error']);
            }
            //多语言
            if($this->islan && isset($change['lans'])){
                //获取原多语言值
                $m_val=Db::name($table.'_val');
                //只要获得语言id和记录id就可以了
                $lans_info=$m_val->where('pid',$info1['pid'])->column('id','lid');
                
                $vals_add=[]; 
                //不能按字段保存更改，得按语言保存，这样才能添加信息
                //遍历更改记录 ,按语言遍历
                foreach($change['lans'] as $k=>$v){
                    //不存在则添加,存在则更新
                    if(isset($lans_info[$k])){
                        $m_val->where('id',$lans_info[$k])->update($v);
                    } else{
                       $v['pid']=$info['id'];
                       $v['lid']=$k;
                        $vals_add[]=$v;
                    } 
                } 
                //添加
                if(!empty($vals_add)){
                   
                    $m_val->insertAll($vals_add);
                }
               
                unset($change['lans']);
            }
            foreach($change as $k=>$v){
                $update_info[$k]=$v;
            }
            
            $row=$m->where('id',$info1['pid'])->update($update_info);
            if($row!==1){
                $m->rollback();
                $this->error('信息更新失败，请刷新后重试');
            }
            //审核后的操作
            $res=$this->edit_review_after($info1,$change);
            if(!($res>0)){
                $m->rollback();
                $this->error($res);
            }
           
        }
       
        //审核成功，记录操作记录,发送审核信息
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'审核'.$info1['aid'].'-'.$info1['aname'].'对'.($this->flag).$info['id'].'-'.$info['name'].'的编辑为'.$review_status[$status],
            'table'=>$table,
            'type'=>'edit_review',
            'pid'=>$info1['pid'],
            'link'=>url('edit_info',['id'=>$info1['id']]),
            'shop'=>$admin['shop'],
        ];
        
        zz_action($data_action,['aid'=>$info1['aid']]);
        
        $m->commit();
        $this->success('审核成功');
    }
    /**
     * 编辑记录批量删除
     */
    public function edit_del_all()
    {
        if(empty($_POST['ids'])){
            $this->error('未选中信息');
        }
        $eids=$_POST['ids'];
        
        $admin=$this->admin;
        $table=$this->table;
        $m_edit=Db::name('edit');
        $time=time();
        $where=[
            'e.id'=>['in',$eids],
            'e.table'=>['eq',$table],
        ];
        
        //其他店铺检查,如果没有shop属性就只能是1号主站操作,有shop属性就带上查询条件
        if($admin['shop']!=1){
            $where['e.shop']=['eq',$admin['shop']];
        }
        
        //得到要删除的数据
        $list=$m_edit
        ->alias('e')
        ->join('cmf_'.$table.' p','p.id=e.pid','left')
        ->where($where)
        ->column('e.*,p.name as pname');
        
        if(empty($list)){
            $this->error('没有要删除的数据');
        }
        $eidss=implode(',',array_keys($list));
         
        $m_edit->startTrans();
        //id 删除
        $where_edit=[
            'table'=>['eq',$table],
            'id'=>['in',$eids],
        ];
        
        $rows=$m_edit->where($where_edit)->delete();
        if($rows<=0){
            $m_edit->rollback();
            $this->error('没有删除数据');
        }
        //删除编辑详情
        Db::name('edit_info')->where(['eid'=>['in',$eids]])->delete();
        
        //审核成功，记录操作记录,发送审核信息
        $flag=$this->flag;
        //记录操作记录
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'批量删除'.$flag.'编辑记录('.$eidss.')',
            'table'=>$table,
            'type'=>'edit_del',
            'link'=>'',
            'pid'=>0,
            'shop'=>$admin['shop'],
        ];
        
        zz_action($data_action,['eids'=>$eidss]);
        
        $m_edit->commit();
        $this->success('已批量删除'.$rows.'条数据');
    }
    /**
     * 分类信息,1-index,2-edit_index,3-add,edit,edit_info
     *   */
    public function cates($type=3){
        $admin=$this->admin;
         $isshop=$this->isshop;
        if($type<3){
           
            //显示编辑人和审核人
            $m_user=Db::name('user');
            //可以加权限判断，目前未加
            //创建人
            $where_aid=[
                'user_type'=>1, 
                'user_status'=>1,
            ];
            if($isshop){
                if($admin['shop']>1){
                    $where_aid['shop']=$admin['shop'];
                }
            }else{
                $where_aid['shop']=1;
            }
           
            $aids=$m_user->where($where_aid)->order('shop asc')->column('id,user_nickname');
            //审核人
            $where_rid=[
                'user_type'=>1,
                'shop'=>1,
                'user_status'=>1,
            ];
            $rids=$m_user->where($where_rid)->order('shop asc')->column('id,user_nickname');
            $this->assign('aids',$aids);
            $this->assign('rids',$rids);
            
            //如果分店铺又是列表页查找,显示所有店铺
            if($isshop){
                //编辑记录,加盟商，展示
                $where=[
                    'status'=>2
                ]; 
                if($type==2){
                    //编辑记录,加盟商，展示 
                    if($admin['shop']>1){
                        $where['id']=['in',[1,$admin['shop']]];
                    }
                    $shops=Db::name('shop')->where($where)->column('id,name'); 
                    $this->assign('shops',$shops);
                }elseif($admin['shop']==1){
                    $shops=Db::name('shop')->where($where)->column('id,name'); 
                    $this->assign('shops',$shops);
                }
            }
            
        }else{
            //分语言
            if($this->islan){ 
                $this->assign('lans', session('admin_lans'));
                $this->assign('vals',$this->vals);
            }
        }
         
    }
    /**
     * 参数值过滤
     *   */
    public function param_check(&$data){
        if(empty($data['name'])){
            return '名称必须填写';
        }
        if(isset($data['id'])){
            $data['id']=intval($data['id']);
        }
        $fields=$this->base;
        //str,int,round1,round2,round3,round4,pic,file
     
        //添加的信息
        foreach($fields as $k=>$v){
            if(isset($data[$k])){
                switch($v){
                    case 'int':
                        $data[$k]=intval($data[$k]);
                        break;
                    case 'round1':
                        $data[$k]=round($data[$k],1);
                        break;
                    case 'round2':
                        $data[$k]=round($data[$k],2);
                        break;
                    case 'round3':
                        $data[$k]=round($data[$k],3);
                        break;
                    case 'round4':
                        $data[$k]=round($data[$k],4);
                        break;
                    default:
                        $data[$k]=$data[$k];
                        break;
                }
            }
            
        }
        return 1;
    }
   
    /* 编辑审核前的操作 */
    public function edit_review_before($info1){
        //得到修改的字段
        $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
        $change=json_decode($change,true);
        return $change;
    }
    /* 编辑审核后的操作 */
    public function edit_review_after($info1){
        return 1;
    }
    /* 审核后的操作 */
    public function review_after($info,$status){
        //审核成功，记录操作记录,发送审核信息
        $statuss=$this->statuss;
        $admin=$this->admin;
        
        $time=time();
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>time(),
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'审核'.($this->flag).$info['id'].'-'.$info['name'].'的状态为'.$statuss[$status],
            'table'=>($this->table),
            'type'=>'review',
            'pid'=>$info['id'],
            'link'=>url('edit',['id'=>$info['id']]),
            'shop'=>$admin['shop'],
        ];
        zz_action($data_action,['aid'=>$info['aid']]);
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        return 1;
    }
    /* 查看详情 */
    public function edit_after($info){
        
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
       
    }
    /* 删除前 */
    public function del_before($ids){
        return 1;
    }
}

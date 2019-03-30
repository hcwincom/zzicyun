<?php

namespace app\money\controller;

 
use think\Db;
use cmf\controller\AdminBaseController;
use app\money\model\CreditGetModel;
 

class AdminCreditApplyController extends AdminBaseController
{
    private $m;
    private $review_statuss;
    public function _initialize()
    {
        parent::_initialize();
        $this->m=Db::name('credit_apply');
        $this->review_statuss=config('review_status');
        $this->assign('review_statuss', $this->review_statuss);
        $this->assign('flag','信用提额申请');
        $this->assign("money_types", config('money_type'));
        
        
    }
    /**
     * 信用提额申请列表
     * @adminMenu(
     *     'name'   => '信用提额申请列表',
     *     'parent' => 'money/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1,
     *     'icon'   => '',
     *     'remark' => '信用提额申请列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data=$this->request->param();
        $where=[];
        //审核状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['ca.status']=$data['status'];
        }
        
        //审核人
        if(empty($data['aid'])){
            $data['aid']=0;
        }else{
            $where['ca.aid']=$data['aid'];
        }
        //申请类型
        if(empty($data['money_type'])){
            $data['money_type']=0;
        }else{
            $where['ca.type']=$data['money_type'];
        }
        //查询字段
        $types=[2=>['u.user_nickname','用户名'],1=>['ca.uid','用户id']];  
        $search_types=config('search_types');
        zz_search_param($types,$search_types,$data,$where);
        
        
        //时间类别
        $times=[1=>['ca.utime','申请时间'],2=>['ca.atime','审核时间']];
        zz_search_time($times,$data,$where);
        $m=$this->m;
        $list=$m
        ->alias('ca')
        ->join('cmf_user u','u.id=ca.uid')
        ->field('ca.*,u.user_nickname as uname')
        ->where($where)
        ->paginate();
        $page=$list->appends($data)->render();
        
        //显示编辑人和审核人
        $m_user=Db::name('user');
        //可以加权限判断，目前未加
        //创建人
        $where_aid=[
            'user_type'=>1,
            'user_status'=>1,
            'shop'=>1,
        ];
         
        $aids=$m_user->where($where_aid)->order('id asc')->column('id,user_nickname');
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('aids',$aids);
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types); 
        return $this->fetch();
    }
    
    
    
    /**
     * 信用提额申请详情
     * @adminMenu(
     *     'name'   => '信用提额申请详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '信用提额申请详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id=$this->request->param('id',0,'intval');
        $m=$this->m;
        $info=$m
        ->alias('ca')
        ->join('cmf_user u','u.id=ca.uid')
        ->join('cmf_user a','a.id=ca.aid','left')
        ->field('ca.*,u.user_nickname as uname,a.user_nickname as aname')
        ->where('ca.id',$id)
        ->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    /**
     * 信用提额申请审核
     * @adminMenu(
     *     'name'   => '信用提额申请审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '信用提额申请审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        $status=$this->request->param('status',0,'intval');
        $id=$this->request->param('id',0,'intval');
        $adsc=$this->request->param('adsc','');
        if($status<2 || $status>3 || $id<=0){
            $this->error('信息错误');
        }
        $m=$this->m; 
        //查找信息
        $info=$m->where('id',$id)->find();
       
        if(empty($info) || $info['status'] != 1){
            $this->error('信息不存在');
        }
        $admin=$this->admin;
         
        $time=time();
        $update=[
            'aid'=>$admin['id'],
            'atime'=>$time,
            'status'=>$status, 
            'adsc'=>$adsc
        ];
        $m->startTrans();
        $row=$m->where('id',$id)->update($update);
        
        if($row!==1){
            $m->rollback();
            $this->error('审核失败，请刷新后重试');
        }
        //提额
        if($status==2){
            $m_get=new CreditGetModel();
            $m_get->num_do($info['uid'], $info['num'], '申请提额通过', $info['type']);
        }
         
        //审核成功，记录操作记录,发送审核信息
        $statuss=$this->review_statuss;
        $admin=$this->admin;
        
        $time=time();
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>time(),
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'审核信用提额申请'.$info['id'].'为'.$statuss[$status],
            'table'=>'credit_apply',
            'type'=>'review',
            'pid'=>$info['id'],
            'link'=>url('edit',['id'=>$info['id']]),
            'shop'=>$admin['shop'],
        ];
        zz_action($data_action,['aid'=>$info['aid']]);
        $m->commit();
        $this->success('审核成功');
    }
    /**
     * 信用提额申请状态批量同意
     * @adminMenu(
     *     'name'   => '信用提额申请状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '信用提额申请状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    
    
    
     
}

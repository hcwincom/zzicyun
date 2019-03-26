<?php

namespace app\coupon\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;
use app\coupon\model\CouponModel;

class AdminCouponUserController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='用户优惠券';
        $this->table='coupon_user';
        $this->m=Db::name('coupon_user');
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=0;
       
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
        
    }
    /**
     * 用户优惠券列表
     * @adminMenu(
     *     'name'   => '用户优惠券列表',
     *     'parent' => 'coupon/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券列表',
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
        //用户名
        $uids=[];
       
        $unames=[];
        if(!empty($list)){
            foreach($list as $v){
                $uids[]=$v['uid'];
            }
            $m_user=Db::name('user');
            $unames=$m_user->where('id','in',$uids)->column('id,user_login');
        }
      
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('unames',$unames);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
        return $this->fetch();
    }
    
    
    /**
     * 用户优惠券添加
     * @adminMenu(
     *     'name'   => '用户优惠券添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        
        //可添加的优惠券 
        $m_coupon=Db::name('coupon');
        $list= $m_coupon->where(function ($query) {
            $query->where([
                'status'=>2,
                'type'=>1, 
            ]);
        })->where(function ($query) {
            $query->whereOr([ 
                'day'=>['gt',0],
                'time2'=>['egt',strtotime(date('Y-m-d'))]
            ]);
        })->column('');
        $this->assign('coupons',$list);
        $this->cates();
        return $this->fetch();
        
    }
    /**
     * 用户优惠券添加do
     * @adminMenu(
     *     'name'   => '用户优惠券添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        $m=$this->m;
        $data=$this->request->param();
        
        $url=url('index');
        
        $table=$this->table;
        $time=time();
        $admin=$this->admin;
       
        //str,int,round1,round2,round3,round4,pic,file
        $data_add=[];
        if(empty($data['coupon']) || empty($data['uids'])){
            $this->error('优惠券和用户为必选');
        }
        $data_add['status']=2;
        $data_add['name']=$data['name'];
        $data_add['dsc']=$data['dsc']; 
        $data_add['coupon']=$data['coupon']; 
        $data_add['aid']=$admin['id'];
        $data_add['atime']=$time;
        $data_add['rid']=$admin['id'];
        $data_add['rtime']=$time;
        $data_add['time']=$time;
        $m_coupon=Db::name('coupon');
        $coupon=$m_coupon->where('id',$data['coupon'])->find();
         if(empty($data['name'])){
             $data_add['name']=$coupon['name'];
         }
         $data_add['sort']=$coupon['sort'];
         $data_add['type']=$coupon['type'];
         $data_add['money']=$coupon['money'];
         $data_add['money_min']=$coupon['money_min'];
         $data_add['sort']=$coupon['sort'];
         $time0=strtotime(date('Y-m-d'),$time);
         if(empty($coupon['time1'])){
             $data_add['time1']=$time0;
             $data_add['time2']=$data_add['time1']+24*3600*($coupon['day']-1);
         }else{
             $data_add['time1']=$coupon['time1'];
             $data_add['time2']=$coupon['time2'];
         }
         if($data_add['time1']>$data_add['time2'] || $data_add['time2']<$time0){
             $this->error('起止时间错误');
         }
         if($data_add['time1']<$time){
             $data_add['status_time']=1;
         }else{
             $data_add['status_time']=2;
         }
         $data_adds=[];
         foreach($data['uids'] as $v){
             $data_add['uid']=$v;
             $data_adds[]=$data_add;
         }
        
        $m->insertAll($data_adds);
          
        $this->success('添加成功');
        
    }
    /**
     * 用户优惠券详情
     * @adminMenu(
     *     'name'   => '用户优惠券详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 用户优惠券状态审核
     * @adminMenu(
     *     'name'   => '用户优惠券状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 用户优惠券状态批量同意
     * @adminMenu(
     *     'name'   => '用户优惠券状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 用户优惠券状态禁用
     * @adminMenu(
     *     'name'   => '用户优惠券状态禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券状态禁用',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        parent::ban();
    }
    /**
     * 用户优惠券状态恢复
     * @adminMenu(
     *     'name'   => '用户优惠券状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 用户优惠券批量删除
     * @adminMenu(
     *     'name'   => '用户优惠券批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户优惠券批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        
        parent::del_all();
    }
    /**
     * 分类信息
     * {@inheritDoc}
     * @see \app\common\controller\AdminInfoController::cates()
     */
    public function cates($type=3){
        parent::cates($type);
        $this->assign('coupon_types',config('coupon_type'));
        $utypes=config('user_search');
        $search_types=config('search_types');
        $this->assign('utypes',$utypes);
        $this->assign('search_types',$search_types); 
        $this->assign('user_rates',config('user_rates')); 
        $this->assign('user_cates',config('user_cates')); 
       
    }
    
    public function param_check(&$data){
        
        return 1;
    }
     
}

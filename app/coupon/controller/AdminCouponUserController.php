<?php

namespace app\coupon\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;
use app\coupon\model\CouponModel;
use app\coupon\model\CouponUserModel;

class AdminCouponUserController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='用户优惠券';
        $this->table='coupon_user';
        $this->m=new CouponUserModel();
        
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
        
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        //类型
        if(empty($data['coupon_type'])){
            $data['coupon_type']=0;
        }else{
            $where['type']=['eq',$data['coupon_type']];
        }
        //类型
        if(empty($data['money_type'])){
            $data['money_type']=0;
        }else{
            $where['money_type']=['eq',$data['money_type']];
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
         
        $admin=$this->admin;
       
        $m->startTrans();
        $res=$m->user_gets($data, $admin);
        if(!($res>0)){
            $m->rollback();
            $this->error($res);
        }
        $m->commit();
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
        $this->assign('money_types3',config('money_type3'));
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

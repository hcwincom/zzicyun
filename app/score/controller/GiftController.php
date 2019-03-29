<?php
 
namespace app\score\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController;
use app\user\model\UserAddressModel;
use app\notice\model\NoticeConfValModel;
use app\user\model\UserModel;
use app\user\model\UserIndustryModel;
use app\score\model\GiftModel;
use app\score\model\GiftCateModel;
use app\score\model\GiftUserModel;
use app\score\model\ScoreUserModel;
use app\score\model\ScoreRuleModel;
 

class GiftController extends UserBaseController
{ 
    public function _initialize()
    {
        
        parent::_initialize();
       
    }
    /**
     * 礼品中心
     */
    public function gift()
    {
        
        //用户信息
        $uid=session('user.id');
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
        session('user',$user); 
        $this->assign('user',$user);
        //礼品分类
        $m_gift_cate=new GiftCateModel();
        $gift_cates=$m_gift_cate->get_names();
        $this->assign('gift_cates',$gift_cates);
        //礼品
        $m_gift=new GiftModel();
        $list=$m_gift->get_list($gift_cates,4);
       
        $this->assign('list_search',$list[0]);
        unset($list[0]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 礼品详情
     */
    public function gift_detail()
    {
       
        //用户信息
        $uid=session('user.id');
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
        session('user',$user);
        $this->assign('user',$user);
        //礼品分类
        $m_gift_cate=new GiftCateModel();
        $gift_cates=$m_gift_cate->get_names();
        $this->assign('gift_cates',$gift_cates);
        //礼品
        $id=$this->request->param('id',0,'intval');
        if(!($id>0)){
            $this->error('数据错误');
        }
        $m_gift=new GiftModel();
        $info=$m_gift->get_one($id);
       
        $this->assign('info',$info);
        return $this->fetch();
    }
    /**
     * 礼品列表
     */
    public function gift_list()
    {
        
        $data=$this->request->param();
        $where=['status'=>2];
        if(empty($data['cid'])){
            $data['cid']=0;
        }else{
            $where['cid']=$data['cid'];
        }
        if(empty($data['name'])){
            $data['name']='';
        }else{
            $where['name']=['like','%'.$data['name'].'%'];
        }
        //积分范围
        $scores=[
            0=>'不限',
            1=>'0-1000',
            2=>'1000-5000',
            3=>'5000-10000',
            4=>'10000-20000',
            5=>'20000以上',
        ];
        if(empty($data['score'])){
            $data['score']=0;
        }else{
            switch ($data['score']){
                case 1:
                    $where['score']=['lt',1000];
                    break;
                case 2:
                    $where['score']=['between',[1000,5000]];
                    break;
                case 3:
                    $where['score']=['between',[5000,10000]];
                    break;
                case 4:
                    $where['score']=['between',[10000,20000]];
                    break;
                case 5:
                    $where['score']=['gt',20000];
                    break;
            }
        }
        //礼品分类
        $m_gift_cate=new GiftCateModel();
        $gift_cates=$m_gift_cate->get_names();
        $this->assign('gift_cates',$gift_cates);
        //礼品
        $m_gift=new GiftModel();
        $res=$m_gift->get_page($where, $data);
        
        $this->assign('list',$res['list']);
        $this->assign('page',$res['page']); 
        $this->assign('scores',$scores); 
        $this->assign('data',$data); 
        return $this->fetch();
    }
    /**
     * 礼品兑换页面
     */
    public function gift_do()
    {
        //进入礼品兑换页面
        $data=$this->request->param();
     
        if(empty($data['id'])){
            $this->error('数据错误');
        }
        if(empty($data['num'])){
            $data['num']=1;
        }
        
        //用户信息
        $uid=session('user.id');
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
        session('user',$user);
        $this->assign('user',$user);
        
        //礼品 gh
        $m_gift=new GiftModel();
        $info=$m_gift->get_one($data['id']);
        $data['score_count']=$info['score']*$data['num'];
        if($data['score_count'] <= 0 ){
            $this->error('数据错误');
        }
        //收货地址
        $m_address=new UserAddressModel();
        $addresses=$m_address->get_all($uid,0);
        $this->assign('addresses',$addresses);
        $data['address_id']=key($addresses); 
        $this->assign('info',$info);
        $this->assign('data',$data); 
        return $this->fetch();
    }
    /**
     * 礼品兑换
     */
    public function gift_exchange()
    {
        //进入礼品兑换页面
        $data=$this->request->param();
        
        if(empty($data['id']) || empty($data['num'])){
            $this->error('数据错误');
        }
        $uid=session('user.id');
        $m_gu=new GiftUserModel();
        $m_gu->startTrans();
        $res=$m_gu->gift_exchange($data,$uid);
        if(!($res>0)){
            $this->error($res);
        }
        $m_gu->commit();
        $this->success('ok');
    }
    /**
     * 积分记录
     */
    public function score_list()
    {
        $data=$this->request->param();
        $uid=session('user.id');
        $where=[
            'uid'=>$uid,
        ];
        //积分规则
        if(empty($data['pid'])){
            $data['pid']=0;
        }else{
            $where['pid']=$data['pid'];
        }
        //积分变化时间
        $time1=0;
        $tiem2=0;
        if(empty($data['start'])){
            $data['start']='';
            $time1=0;
        }else{
            $time1=strtotime($data['start']);
        }
        if(empty($data['end'])){
            $data['end']='';
            $time2=0;
        }else{
            $time2=strtotime($data['end']);
        }
         if($time1>$time2){
             $this->error('时间选择错误');
         }
         if($time1>0){
             if($time2>0){
                 $where['time']=['between',[$time1,$time2]];
             }else{
                 $where['time']=['gt',$time1];
             }
         }elseif($time2>0){
             $where['time']=['lt',$time1];
         }
        $m_gu=new ScoreUserModel();
        $res=$m_gu->get_page($uid,$where,$data);
        $list=empty($res['list'])?[]:$res['list'];
        $page=empty($res['page'])?null:$res['page'];
        
        //积分规则
        $m_score_rule=new ScoreRuleModel();
        $rules=$m_score_rule->get_names();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('rules',$rules);
        $this->assign('data',$data);
        return $this->fetch();
    }
}
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
 
/**
 * 礼品兑换记录
 */
class GiftUserController extends UserBaseController
{ 
    public function _initialize()
    {
        
        parent::_initialize();
       
    }
    /**
     * 礼品兑换记录
     */
    public function gift_user_list()
    {
        
        //用户信息
        $uid=session('user.id');
        $m_gu=new GiftUserModel();
        $res=$m_gu->get_page($uid);
        $list=empty($res['list'])?[]:$res['list'];
        $page=empty($res['page'])?null:$res['page'];
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    /**
     * 礼品详情
     */
    public function gift_user_detail()
    { 
        $uid=session('user.id');
        //礼品
        $id=$this->request->param('id',0,'intval');
        if(!($id>0)){
            $this->error('数据错误');
        }
        $m_gu=new GiftUserModel();
        $info=$m_gu->get_one($id,$uid);
      
        $this->assign('info',$info);
        return $this->fetch();
    }
    
}
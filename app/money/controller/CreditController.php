<?php
 
namespace app\money\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController; 
use app\user\model\UserModel;  
use app\money\model\CreditGetModel;
use app\money\model\CreditApplyModel;

class CreditController extends UserBaseController
{

    public function _initialize()
    {
        
        parent::_initialize();
        $lan1=$this->lan1;
        if($lan1!=1){
            $this->redirect(url('/'));
        }
    }
    /**
     * 授信额度
     */
    public function credit_index()
    {
       
        $uid=session('user.id');
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
       
        //授信记录
        $m_credit_get=new CreditGetModel();
        $list_get=$m_credit_get->get_list($uid);
        $this->assign('user',$user);
        $this->assign('list_get',$list_get); 
        return $this->fetch();
    }
    /**
     * 授信额度申请
     */
    public function credit_apply()
    {
       
        $uid=session('user.id');
        $data=$this->request->param();
        foreach($data as $k=>$v){
            $data[$k]=intval($v);
            if($data[$k]<=0){
                $this->error('数据错误');
            }
        }
       
        $data_add=[
            'uid'=>$uid,
            'utime'=>time(),
            'type'=>$data['type'],
            'num'=>$data['num'],
            'buy_old'=>$data['num_old'],
            'buy_new'=>$data['num_new'], 
        ];
      
        $m_apply=new CreditApplyModel();
        $m_apply->apply($data_add);
        $this->success('已提交申请，等待后台申请');
        
    }
    
}
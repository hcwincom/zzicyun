<?php
 
namespace app\user\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController;
use app\user\model\UserAddressModel;
use app\notice\model\NoticeConfValModel;
use app\user\model\UserModel;
use app\user\model\UserIndustryModel;
use app\express\model\AreaModel;

class InfoController extends UserBaseController
{

    /**
     * 个人信息 
     */
    public function info_base()
    {
        $lan1=$this->lan1;
        $lan2=$this->lan2;
        $uid=session('user.id');
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
        session('user',$user);
        //会员等级user_rates
        $m_conf=new NoticeConfValModel();
        $user_rates=$m_conf->get_one($lan1,$lan2,'user_rates');
        $user['rate_name']=empty($user_rates[$user['user_rate']])?$user['user_rate']:$user_rates[$user['user_rate']];
        //用户类型
        $user_cates=$m_conf->get_one($lan1,$lan2,'user_cates');
        //用户行业
        $m_industry=new UserIndustryModel();
        $industrys=$m_industry->get_all($lan1, $lan2);
        $this->assign('user',$user);
     
        $this->assign('industrys',$industrys);
        $this->assign('user_cates',$user_cates);
        return $this->fetch();
    }
    /**
     * 个人信息修改
     */
    public function info_base_do()
    {
        $lan1=$this->lan1;
        $lan2=$this->lan2;
        $uid=session('user.id');
        $m_user=new UserModel();
    
       $data=$this->request->param();
       $update=[
           'user_nickname'=>$data['user_nickname'],
           'user_industry'=>$data['user_industry'],
           'user_cate'=>$data['user_cate'],
           'company_name'=>$data['company_name'],
           'country'=>$data['country'],
           'province'=>$data['province'],
           'city'=>$data['city'],
           'area'=>$data['area'],
           'address'=>$data['address'], 
       ];
       $m_area=new AreaModel();
       $update['address_info']=$m_area->get_addressinfo($update,$lan1,$lan2);
       
       $user=$m_user->where('id',$uid)->update($update);
       $this->success('ok');
    }
    /* 头像修改 */
    public function ajax_avatar(){
        set_time_limit(300);
        $user=session('user');
        if(empty($_FILES['avatar'])){
            $this->error('file_chose');
        }
        $file=$_FILES['avatar'];
        zz_log(json_encode($file));
        if($file['error']==0){
            if($file['size']>5000000){
                $this->error('file_too_long');
            }
            $pathid='avatar/'.$user['id'].'/';
            
            $path='upload/';
            //没有目录创建目录
            if(!is_dir($path.$pathid)){
                mkdir($path.$pathid);
            }
            //获取后缀名,复制文件
            $ext=substr($file['name'], strrpos($file['name'],'.'));
            //存到临时文件
            $destination=$path.$pathid.time().$ext;
          
            if(move_uploaded_file($file['tmp_name'], $destination)){
                $pic_new=zz_set_file($destination,$pathid,config('pic_avatar'));
                $m_user=new UserModel();
                $m_user->where('id',$user['id'])->setField('avatar',$pic_new);
                if(is_file($path.$user['avatar'])){
                    unlink($path.$user['avatar']);
                }
                $user=$m_user->get_user($user['id']);
                session('user',$user);
                $this->success('success','');
            }else{
                $this->error('file_upload_faild');
            }
        }else{
            $this->error('file_upload_faild');
        }
    }
    /**
     * 收货地址
     */
    public function address()
    {
        $uid=session('user.id');
        $type=$this->request->param('type',1,'intval');
        $m_address=new UserAddressModel();
        $list=$m_address->get_all($uid,$type);
        $this->assign('type',$type);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 收货地址修改
     */
    public function address_do()
    {
       
        $lan1=$this->lan1;
        $lan2=$this->lan2;
        $uid=session('user.id');
        $m_address=new UserAddressModel();
        
        $data=$this->request->param();
        
        $update=[ 
            'accept_name'=>$data['accept_name'],
            'tel'=>$data['tel'], 
            'province'=>intval($data['province']),
            'city'=>intval($data['city']),
            'area'=>intval($data['area']),
            'address'=>$data['address'],
            'is_first'=>$data['is_first'],
        ];
        //默认中国
        $update['country']=(empty($data['country']))?1:intval($data['country']);
        if(empty($update['province']) || empty($update['city']) || empty($update['area'])){
            $this->error('error_input');
        }
        $m_area=new AreaModel(); 
        $update['address_info']=$m_area->get_addressinfo($update,$lan1,$lan2);
        $m_address->startTrans();
        if($update['is_first']==1){
            $m_address->where('uid',$uid)->setField('is_first',2);
        }
        if(empty($data['id'])){
            $update['uid']=$uid;
            $update['type']=$data['type'];
            $m_address->insert($update);
        }else{
            $where=[
                'id'=>$data['id'],
                'uid'=>$uid
            ];
            $m_address->where($where)->update($update);
        }
        $m_address->commit();
        $this->success('ok');
    }
    /**
     * 收货地址删除
     */
    public function address_del()
    {
        $id=$this->request->param('id',0,'intval');
        $uid=session('user.id');
        if(empty($id)){
            $this->error('data_error');
        }
        $m_address=new UserAddressModel();
        $where=['id'=>$id,'uid'=>$uid];
        $row=$m_address->where($where)->delete();
        if($row==1){
            $this->success('ok');
        }else{
            $this->error('error_delete');
        } 
    }
    /**
     * 收货地址设为默认
     */
    public function address_first()
    {
        $id=$this->request->param('id',0,'intval');
        $uid=session('user.id');
        if(empty($id)){
            $this->error('data_error');
        }
        //先清除所有默认再单独设置默认
        $m_address=new UserAddressModel();
        $where=['uid'=>$uid];
        $m_address->where($where)->setField('is_first',2);
        $where['id']=$id;
        $m_address->where($where)->setField('is_first',1);
        $row=$m_address->where($where)->update();
        $this->success('ok');
        
    }
    /**
     * 密码修改
     */
    public function psw_change()
    {
        $this->assign('user',session('user'));
        return $this->fetch();
    }
    /**
     *密码修改
     */
    public function psw_change_do()
    {
        
        $uid=session('user.id');
        $m_user=new UserModel();
        
        $data=$this->request->param();
        $old=cmf_password($data['old']);
        $where=[
            'id'=>$uid,
            'user_pass'=>$old,
        ];
        $user=$m_user->where($where)->find();
        if(empty($user)){
            $this->error('error_psw');
        }
        $new=cmf_password($data['psw']);
        $res=$m_user->where('id',$uid)->setField('user_pass',$new);
        $this->success('ok');
    }
     
}
<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use think\Validate; 
use app\user\model\UserModel;
use think\Db;
use app\common\controller\DeskBaseController;
use app\score\model\ScoreUserModel;
use app\notice\model\BannerModel;
class LoginController extends DeskBaseController
{

    
    /**
     * 登录
     */
    public function index()
    {
        $redirect = $this->request->post("redirect");
        if (empty($redirect)) {
            $redirect = $this->request->server('HTTP_REFERER');
        } else {
            $redirect = base64_decode($redirect);
        }
        session('login_http_referer', $redirect);
        if (cmf_is_user_login()) { //已经登录时直接跳到首页
            return redirect($this->request->root() . '/');
        } else {
            $this->redirect('login');
        }
    }
    /**
     * 登录页面
     */
    public function login()
    {
        if(empty($_SERVER['HTTP_REFERER'])){
            $url=url('user/Center/center');
        }else{
            $url=$_SERVER['HTTP_REFERER'];
        }
      
        //广告图
        $m_banner=new BannerModel();
        $banner=$m_banner->get_banner_by_cname('login_banner');
        $this->assign('banner',$banner);
        $this->assign('url',$url);
        return $this->fetch();
    }
    /**
     * 退出登录
     */
    public function logout()
    {
       session('user',null);
       $this->redirect(url('/'));
    }
    /**
     * 前台用户注册提交
     */
    public function login_ajax()
    {
        //{'phone':phone,'psw':psw,'email':email,'code':code},
        if ($this->request->isPost()) {
            
            $time=time();
            $data= $this->request->post();
            $reg=config('reg');
            $where=[];
             if(empty($data['code'])){
                 //账号密码登录
                 $where['user_pass']=cmf_password($data['psw']);
                 //手机号验证
                 if(preg_match($reg['mobile'][0], $data['name'])==1){
                     $where['mobile']=$data['name'];
                 }elseif(preg_match($reg['user_login'][0], $data['name'])==1){
                     $where['user_login']=$data['name'];
                 }else{
                     $this->error('username_error');
                 }
                 
             }else{
                 //手机号登录
                 if(preg_match($reg['mobile'][0], $data['name'])==1){
                     $where['mobile']=$data['name'];
                 }else{
                     $this->error('mobile_error');
                 }
                 unset($data['code']);
             }
             
            $m_user=Db::name('user');
            $user=$m_user->where($where)->find();
            if(empty($user)){
                $this->error('login_error');
            }
            
            //拉黑判断。
            if ($user['user_status'] == 0) {
                $this->error("login_ban");
            }
           
            $m_user->startTrans();
            $data = [
                'last_login_time' => time(),
                'last_login_ip'   => get_client_ip(0, true),
            ];
            $m_user->where('id',$user['id'])->update($data);
            //登录积分
            $m_score=new ScoreUserModel();
            $m_score->score_do($user['id'], 'login');
            $m_user->commit();
            session('user', $user);
            
            $this->success("login_success");
        } else {
            $this->error("request_error");
        }
        
    }
    
    /**
     * 找回密码
     */
    public function psw_forget()
    {
        return $this->fetch();
    }

    /**
     * 用户密码重置
     */
    public function passwordReset()
    {

        if ($this->request->isPost()) {
            $validate = new Validate([
                'captcha'           => 'require',
                'verification_code' => 'require',
                'password'          => 'require|min:6|max:32',
            ]);
            $validate->message([
                'verification_code.require' => '验证码不能为空',
                'password.require'          => '密码不能为空',
                'password.max'              => '密码不能超过32个字符',
                'password.min'              => '密码不能小于6个字符',
                'captcha.require'           => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $captchaId = empty($data['_captcha_id']) ? '' : $data['_captcha_id'];
            if (!cmf_captcha_check($data['captcha'], $captchaId)) {
                $this->error('验证码错误');
            }

            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }

            $userModel = new UserModel();
            if ($validate::is($data['username'], 'email')) {

                $log = $userModel->emailPasswordReset($data['username'], $data['password']);

            } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $userModel->mobilePasswordReset($data['username'], $data['password']);
            } else {
                $log = 2;
            }
            switch ($log) {
                case 0:
                    $this->success('密码重置成功', $this->request->root());
                    break;
                case 1:
                    $this->error("您的账户尚未注册");
                    break;
                case 2:
                    $this->error("您输入的账号格式错误");
                    break;
                default :
                    $this->error('未受理的请求');
            }

        } else {
            $this->error("请求错误");
        }
    }


}
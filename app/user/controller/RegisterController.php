<?php
 
namespace app\user\controller;
 
use think\Validate;
use app\user\model\UserModel;
use app\common\controller\DeskBaseController;
use think\Db;
use app\score\model\ScoreUserModel;
use app\notice\model\BannerModel;
class RegisterController extends DeskBaseController
{

   
    /**
     * 前台用户注册
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

        if (cmf_is_user_login()) {
            return redirect($this->request->root() . '/');
        } else {
           $this->redirect('register');
        }
    }
    /**
     * 前台用户注册
     */
    public function register()
    {
        if(empty($_SERVER['HTTP_REFERER'])){
            $url=url('portal/Index/index');
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
     * 前台用户注册提交
     */
    public function register_ajax()
    {
        //{'phone':phone,'psw':psw,'email':email,'code':code},
        if ($this->request->isPost()) {
            $rules = [
                'user_login'=> 'require|min:6|max:20',
                'code'     => 'require|length:6',
                'user_pass' => 'require|min:6|max:32',
                'mobile'=>'require|number|length:11',
                'user_email'=>'require|email',
            ];
             
            $validate = new Validate($rules);
            $validate->message([
                'code.require'     => 'code_error',
                'code.length'     => 'code_error',
                'user_pass.require' => 'psw_error',
                'user_pass.max'     => 'psw_error',
                'user_pass.min'     => 'psw_error',
                'user_login.require' => 'username_error',
                'user_login.max'     => 'username_error',
                'user_login.min'     => 'username_error',
                'mobile.require' => 'mobile_error',
                'mobile.length'     => 'mobile_error',
                'user_email.require'     => 'email_error',
                'user_email.email'     => 'email_error',
            ]);
            $time=time();
            $data1= $this->request->post();
            
            $data=[
                'user_login'=>$data1['username'],
                'user_nickname'=>$data1['username'],
                'code'=>$data1['code'],
                'user_pass'=>$data1['psw'],
                'mobile'=>$data1['mobile'],
                'user_email'=>$data1['email'],
                'last_login_ip'   => get_client_ip(0, true),
                'create_time'     => $time,
                'last_login_time' => $time,
                'user_status'     => 1,
                "user_type"       => 2,//会员
                
            ];
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            unset($data['code']);
            $reg=config('reg');
            //手机号验证
            if(preg_match($reg['mobile'][0], $data['mobile'])!=1){
                $this->error('mobile_error');
            }
            //用户名验证
            if(preg_match($reg['user_login'][0], $data['user_login'])!=1){
                $this->error('username_error');
            }
            $data['user_pass'] = cmf_password($data['user_pass']);
            
            $m_user=Db::name('user');
            $tmp=$m_user->where('mobile',$data['mobile'])->find();
            if(!empty($tmp)){
                $this->error('mobile_used');
            }
            $tmp1=$m_user->where('user_email',$data['user_email'])->find();
            if(!empty($tmp1)){
                $this->error('email_used');
            }
            $tmp2=$m_user->where('user_login',$data['user_login'])->find();
            if(!empty($tmp2)){
                $this->error('username_used');
            }
            $m_user->startTrans();
            $result  = $m_user->insertGetId($data);
            if ($result !== false) { 
                //注册积分
                $m_score=new ScoreUserModel();
                $m_score->score_do($result, 'register');
                //验证码
                session('verify',null);
                $m_user->commit();
                $data   = $m_user->where('id', $result)->find();
                cmf_update_current_user($data);
               
                $this->success("register_success");
            } else {
                $m_user->rollback();
                $this->error("register_error");
            }
             
        } else {
            $this->error("request_error");
        }
        
    }
    /**
     * 前台用户注册提交
     */
    public function doRegister()
    {
        if ($this->request->isPost()) {
            $rules = [
                'captcha'  => 'require',
                'code'     => 'require',
                'password' => 'require|min:6|max:32',

            ];

            $isOpenRegistration = cmf_is_open_registration();

            if ($isOpenRegistration) {
                unset($rules['code']);
            }

            $validate = new Validate($rules);
            $validate->message([
                'code.require'     => '验证码不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过32个字符',
                'password.min'     => '密码不能小于6个字符',
                'captcha.require'  => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $captchaId = empty($data['_captcha_id']) ? '' : $data['_captcha_id'];
            if (!cmf_captcha_check($data['captcha'], $captchaId)) {
                $this->error('验证码错误');
            }

            if (!$isOpenRegistration) {
                $errMsg = cmf_check_verification_code($data['username'], $data['code']);
                if (!empty($errMsg)) {
                    $this->error($errMsg);
                }
            }

            $register          = new UserModel();
            $user['user_pass'] = $data['password'];
            if (Validate::is($data['username'], 'email')) {
                $user['user_email'] = $data['username'];
                $log                = $register->register($user,3);
            } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $register->register($user,2);
            } else {
                $log = 2;
            }
            $sessionLoginHttpReferer = session('login_http_referer');
            $redirect                = empty($sessionLoginHttpReferer) ? cmf_get_root() . '/' : $sessionLoginHttpReferer;
            switch ($log) {
                case 0:
                    $this->success('注册成功', $redirect);
                    break;
                case 1:
                    $this->error("您的账户已注册过");
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
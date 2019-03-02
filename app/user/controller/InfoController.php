<?php
 
namespace app\user\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController;

class InfoController extends UserBaseController
{

    /**
     * 个人信息 
     */
    public function info_base()
    {
        $this->assign('user',session('user'));
        return $this->fetch();
    }
    /**
     * 收货地址
     */
    public function address()
    {
        $this->assign('user',session('user'));
        return $this->fetch();
    }
    /**
     * 密码修改
     */
    public function psw_change()
    {
        $this->assign('user',session('user'));
        return $this->fetch();
    }

     
}
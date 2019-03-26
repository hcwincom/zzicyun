<?php
 
namespace app\user\controller;

 
 
use think\Db;
use app\common\controller\UserBaseController;

class CenterController extends UserBaseController
{

    /**
     * 个人中心 
     */
    public function center()
    {
        $this->assign('user',session('user'));
        return $this->fetch();
    }

     
}
<?php
 
namespace app\common\controller;
 
class UserBaseController extends DeskBaseController
{

    public function _initialize()
    {
//         $this->error('网站暂不开放用户登录和注册,只提供信息浏览功能','/');
//         exit;
        parent::_initialize();
        $this->checkUserLogin();
    }


}
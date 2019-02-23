<?php
 
namespace app\common\controller;
 
class UserBaseController extends DeskBaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->checkUserLogin();
    }


}
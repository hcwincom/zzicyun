<?php
 
namespace app\invoice\controller;
 
 
use think\Db; 
 
use app\common\controller\UserBaseController;
class InvoiceOidController extends UserBaseController
{

    
    /**
     * 开票进程
     */
   public function myinvoice()
   {
      
      
       return $this->fetch();
   }
   public function invoice_infos()
   {
       $type=$this->request->param('type',2,'intval');
       
       return $this->fetch();
   }

}

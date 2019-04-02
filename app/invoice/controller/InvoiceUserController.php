<?php
 
namespace app\invoice\controller;
 
 
use think\Db; 
 
use app\common\controller\UserBaseController;
use app\invoice\model\InvoiceUserModel;
use app\express\model\AreaModel;
class InvoiceUserController extends UserBaseController
{

    /**
     * 用户开票资料
      */
   public function invoice_infos()
   {
       $type=$this->request->param('type',1,'intval');
       $uid=session('user.id');
       $m_invoice=new InvoiceUserModel();
       $list=$m_invoice->get_infos_by_type($uid, $type);
       
       $this->assign('list',$list);
       $this->assign('type',$type); 
       return $this->fetch();
   }
   /**
    * 开票资料更新
    */
   public function invoice_infos_do()
   {
     
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $uid=session('user.id');
       $m_invoice=new InvoiceUserModel();
       
       $data=$this->request->param();
       
       $update=[
           'name'=>$data['name'],  
           'accept_name'=>$data['accept_name'],
           'accept_tel'=>$data['accept_tel'],
           'accept_province'=>intval($data['province']),
           'accept_city'=>intval($data['city']),
           'accept_area'=>intval($data['area']),
           'accept_address'=>$data['accept_address'],
       ];
       if($data['type']<3){ 
           $update['company_address']=$data['company_address'];
           $update['company_tel']=$data['company_tel'];
           $update['bank']=$data['bank'];
           $update['bank_num']=$data['bank_num'];
           $update['company_code']=$data['company_code']; 
         
       } 
       if(empty($update['accept_province']) || empty($update['accept_city']) || empty($update['accept_area'])){
           $this->error('error_input');
       }
       //文件判断
       if(!empty($data['file'])){
           $path='upload/';
           if(is_file($path.$data['file'])){
               $update['file']=$data['file'];
           }
       }
       $m_area=new AreaModel();
       $update['accept_address_info']=$m_area->get_addressinfo($data,$lan1,$lan2,0);
       
       if(empty($data['id'])){
           $update['uid']=$uid;
           $update['type']=$data['type'];
           $id=$m_invoice->insert($update);
       }else{
           $where=[
               'id'=>$data['id'],
               'uid'=>$uid
           ];
           $id=$m_invoice->where($where)->update($update);
       }
       
       $this->success('ok');
   }
   /* 文件上传 */
   public function file_do(){
       set_time_limit(300);
       $uid=session('user.id');
       if(empty($_FILES['file'])){
           $this->error('file_chose');
       }
       $file=$_FILES['file'];
       zz_log(json_encode($file));
       if($file['error']==0){
           if($file['size']>10000000){
               $this->error('file_too_long');
           }
           $pathid='invoice_file/'.$uid.'/';
           
           $path='upload/';
           //没有目录创建目录
           if(!is_dir($path.$pathid)){
               mkdir($path.$pathid);
           }
           //获取后缀名,复制文件
           $ext=substr($file['name'], strrpos($file['name'],'.'));
           //保存文件 
           $file_new=$pathid.time().$ext;
           if(move_uploaded_file($file['tmp_name'], $path.$file_new)){
                
               $this->success('success','',$file_new);
           }else{
               $this->error('file_upload_faild');
           }
       }else{
           $this->error('file_upload_faild');
       }
   }
}

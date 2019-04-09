<?php
 
namespace app\invoice\controller;
 
use app\common\controller\DeskBaseController; 
use think\Db; 
  
class PublicController extends DeskBaseController
{
 
    
   
    /**
     * 文件下载
     */
    public function file_load(){
        
        $id=$this->request->param('id',0,'intval');
        $uid=$this->request->param('uid',0,'intval');
        if($uid==0){
            $uid=session('user.id');
        }
        $where=[
            'id'=>$id,'uid'=>$uid
        ];
        $file=Db::name('invoice_user')->where($where)->value('file');
        if(empty($file)){
            $this->error('no_file');
        }
        $path='upload/';
        $file=$path.$file;
        $filename='invoice_file'.date('Ymd-His');
        if(is_file($file)){
            $fileinfo=pathinfo($file);
            $ext=$fileinfo['extension'];
            $filename=$filename.'.'.$ext;
            header('Content-type: application/x-'.$ext);
            header('content-disposition:attachment;filename='.$filename);
            header('content-length:'.filesize($file));
            readfile($file);
            exit;
        }else{
            $this->error('no_file');
        }
    }
    
    
     
}

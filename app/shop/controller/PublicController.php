<?php
 
namespace app\shop\controller;
 
use app\common\controller\DeskBaseController; 
use think\Db; 
  
class PublicController extends DeskBaseController
{
 
    
   
    /**
     * 文件下载
     */
    public function file_load(){
        
        $id=$this->request->param('id',0,'intval');
        $file_type=$this->request->param('file','file1');
        
        $where=[
            'id'=>$id, 
        ];
        $file=Db::name('shop')->where($where)->value($file_type);
        if(empty($file)){
            $this->error('no_file');
        }
        $path='upload/';
        $file=$path.$file;
        $filename='shopshop_file'.date('Ymd-His');
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

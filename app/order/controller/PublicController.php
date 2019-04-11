<?php
 
namespace app\order\controller;
 
use app\common\controller\DeskBaseController; 
use think\Db; 
use app\express\model\AreaModel;
use app\order\model\OrderCartModel;
 
class PublicController extends DeskBaseController
{
 
    //加入购物车
    public function cart_add(){
        $data=$this->request->param();
        $data=[
            'goods'=>intval($data['goods']),
            'num'=>intval($data['num']),
            'type'=>intval($data['type']), 
        ];
        $uid=session('user.id');
        $session_id=session_id();
        //先检测是否存在
        $where=[
            'type'=>$data['type'],
            'goods'=>$data['goods'],
        ];
        if(empty($uid)){
            $uid=0;
            $where['uid']=$uid;
            $where['session_id']=$session_id;
        } 
        
        $where['uid']=$uid;
        $m_cart=new OrderCartModel();
        $cart=$m_cart->where($where)->find();
        if(empty($cart)){
            //新增
            $data['uid']=$uid;
            $data['session_id']=$session_id;
            $m_cart->insert($data);
        }else{
            $m_cart->where($where)->setInc('num',$data['num']);
        }
        $this->success('ok');
    }
    //运费计算
    public function freight_fee(){
        
    }
    /* 文件上传 */
    public function file_do(){
        set_time_limit(300);
        $oid=$this->request->param('oid',0,'intval'); 
        $type=$this->request->param('type',1,'intval'); 
        $uid=$this->request->param('uid',0,'intval'); 
        if(empty($_FILES['file'])){
            $this->error('file_chose');
        }
        $file=$_FILES['file'];
        $where=['id'=>$oid];
        //有uid是后台，没有是前台，都要检查数据
        if($uid==0){
            $uid=session('user.id');
            $where['uid']=$uid;
        }else{
            $uid=session('ADMIN_ID');
            if(empty($uid)){
                $this->error('data_error'); 
            }
        }
        $m_order=Db::name('order');
        $tmp=$m_order->where($where)->find();
        if(empty($tmp)){
            $this->error('data_error');
        }
         
        if($file['error']==0){
            if($file['size']>10000000){
                $this->error('file_too_long');
            }
            $pathid='order/'.$oid.'/';
            
            $path='upload/';
            //没有目录创建目录
            if(!is_dir($path.$pathid)){
                mkdir($path.$pathid);
            }
            //获取后缀名,复制文件
            $ext=substr($file['name'], strrpos($file['name'],'.'));
            //保存文件
            $time=time();
            $file_new=$pathid.$type.$time.$ext;
            if(move_uploaded_file($file['tmp_name'], $path.$file_new)){
                $data=[
                    'oid'=>$oid,
                    'type'=>$type,
                    'time'=>$time,
                    'uid'=>$uid,
                    'file'=>$file_new,
                    'status'=>($type==1)?2:1,
                ];
                $m_order->startTrans();
                //先废弃原数据，再添加
                $where_update=['oid'=>$oid,'status'=>[1,2],'type'=>$type];
                $data_update=['status'=>3,'rid'=>$uid];
                $m_order->where($where_update)->update($data_update);
                $m_order->insert($data);
                $m_order->commit();
                $this->success('success','',$file_new);
            }else{
                $this->error('file_upload_faild');
            }
        }else{
            $this->error('file_upload_faild');
        }
    }
    
    /**
     * 文件下载
     */
    public function file_load(){
        
        $id=$this->request->param('id',0,'intval');
        $uid=$this->request->param('uid',0,'intval');
        $oid=$this->request->param('oid',0,'intval');
        $type=$this->request->param('type',0,'intval');
        if($uid==0){
            $uid=session('user.id');
        }elseif(empty(session('ADMIN_ID'))){
                $this->error('data_error'); 
        }
        $m_file=Db::name('order_file');
        //有id是后台直接下载，没id是前台下载
        if($id==0){
            $where=['oid'=>$oid,'type'=>$type,'uid'=>$uid]; 
        }else{
            $where=['id'=>$id]; 
        }
        $file=$m_file->where($where)->order('id desc')->value('file');
        if(empty($file)){
            $this->error('no_file');
        }
        $path='upload/';
        $file=$path.$file;
        $filename='order_file'.date('Ymd-His');
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

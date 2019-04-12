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
        $utype=$this->request->param('utype',1,'intval');  
      
        if(empty($_FILES['file'])){
            $this->error('file_chose');
        }
        $file=$_FILES['file'];
        $where=['id'=>$oid];
        //$utype==1是前台2后台，都要检查数据
        if($utype==1){
            $uid=session('user.id');
            $where['uid']=$uid;
        }else{
            $uid=session('ADMIN_ID');
            if(empty($uid)){
                $this->error('data_error'); 
            }
        }
        $m_order=Db::name('order');
        $order=$m_order->where($where)->find();
        if(empty($order)){ 
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
                $m_order->startTrans();
                //合同直接添加，水单添加后还要改变支付状态
                if($type==1){
                    $data=[
                        'oid'=>$oid,
                        'utype'=>$utype,
                        'time'=>$time,
                        'uid'=>$uid,
                        'file'=>$file_new, 
                    ]; 
                    Db::name('order_file')->insert($data);
                }else{
                    $data=[
                        'oid'=>$oid,
                        'oid_type'=>$order['type'],
                        'pay_type'=>4,
                        'type'=>($type==2)?1:2,
                        'uid'=>$uid, 
                        'utime'=>$time, 
                        'file'=>$file_new,
                        'status'=>1,
                        'pay_status_old'=>$order['pay_status'],
                    ];
                    //手续费为0
                    $data['fee']=0;
                    //首次支付还是尾款支付
                    if($data['type']==2){
                        $data['money']=$order['pay2'];
                        $data['money_get']=$order['pay2'];
                        $data['pay_status_new']=4;
                    }else{ 
                        $data['money']=$order['pay1'];
                        $data['money_get']=$order['pay1']; 
                        $data['pay_status_new']=2;
                    } 
                    //先添加数据，然后更新order
                    $m_pay=Db::name('order_pay'); 
                    $m_pay->insert($data); 
                    $where_update=['id'=>$oid,'pay_status'=>$data['pay_status_old']];
                    $data_update=['pay_status'=>$data['pay_status_new'],'pay_type2'=>4];
                    $res=$m_order->where($where_update)->update($data_update); 
                    if(!($res>0)){
                        $m_order->rollback();
                        $this->error('data_error');
                    }
                } 
                $m_order->commit();
                $this->success('success');
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
        $oid=$this->request->param('oid',0,'intval');
        $type=$this->request->param('type',0,'intval');
        $utype=$this->request->param('utype',1,'intval');
        //先检测用户
        if($utype==1){
            $uid=session('user.id');
            $order=Db::name('order')->where(['id'=>$oid,'uid'=>$uid])->find();
            if(empty($order)){
                $this->error('data_error'); 
            }
        }elseif(empty(session('ADMIN_ID'))){
                $this->error('data_error'); 
        }
        //有无id判断
        $where=['oid'=>$oid];
        if($id>0){
            $where['id']=$id;
        } 
        //分辨合同还是水单
        if($type==1){ 
            $m_file=Db::name('order_file');
        }else{ 
            $m_file=Db::name('order_pay');
            $where['type']=($type==2)?1:2;
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

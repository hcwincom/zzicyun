<?php
 
namespace app\store\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
/**
 * 出库操作 
 *
 */
class StoreOutModel extends Model
{
    
    /**
     * 添加出库
     * @param array $data出库数据
     * @param array $admin出库人员 
     * @return string|number 有库存变化时返回出库id,否则返回1
     */
    function instore_add($data,$admin){
        //统一文件锁
        $file = "log/store_lock.txt";
        $fp = fopen($file, "r");
        $store_in_id=0;
        if (flock($fp, LOCK_EX )){
           
            $time=time();
            $data['num']=intval($data['num']);
            
            //产品和数量
            if(empty($data['goods']) || empty($data['num'])){
                return '产品和数量必须选择';
            }
            $m_goods=new GoodsModel();
            //判断供应商
            $goods=$m_goods->where('id',$data['goods'])->find();
            //判断是否足够
            if($goods['store_num']<$data['num']){
                return '产品数量不足';
            }
            $data['shop']=$goods['shop'];
            $data['name']=date('YmdHis').'_'.$admin['id'].'_'.$data['goods'];
            switch($data['type']){
                case 10:
                    //订单出库 
                    $data['about']='';
                    $data['about_name']='';
                     
                    break;
                case 20:
                    //售后出库
                    $data['about']=1;
                    $data['about_name']='售后出库';
                    break;
                case 30:
                    //库存调整
                    $data['about']='';
                    $data['about_name']=''; 
                    break;
                    
            }
            //统一默认为待审核
            $data['status']=1;
            
            $data['aid']=$admin['id'];
            $data['atime']=$time;
            $data['rid']=0;
            $data['rtime']=0; 
            $store_in_id=$this->insertGetId($data);
        }  
             
        flock($fp,LOCK_UN);
        fclose($fp);
        return $store_in_id;
    }
    /**
     * 审核出库 
     * @param array $info 
     * @param array $admin操作员
     * @param number $status审核结果
     * @param string $rdsc审核意见
     * @param string $sns一货一码输入 
     * @return string|number 返回1或错误信息
     *  */
    public function instore_review($info,$admin,$status,$rdsc='确认',$sns=''){
        
        //统一文件锁
        $file = "log/store_lock.txt";
        $fp = fopen($file, "r");
        if (flock($fp, LOCK_EX )){ 
               
            //一货一码,暂时不管
            if(!empty($sns)){
                $m_goods_sn=Db::name('goods_sn');
                $sns=explode(',', $sns);
                //去除重复
                $sns=array_unique($sns);
                $sns0=$m_goods_sn->where('store_in',$info['id'])->column('sn');
                //不要的去掉
                $sn_delete=array_diff($sns0, $sns);
                if(!empty($sn_delete[0])){
                    $where_delete=[
                        'store_in'=>$info['id'],
                        'sn'=>['in',$sn_delete]
                    ];
                    $m_goods_sn->where($where_delete)->delete();
                }
                //新增
                $sn_add=array_diff($sns,$sns0);
                if(!empty($sn_add[0])){
                    $data_sn=[];
                    foreach($sn_add as $v){
                        if(empty($v)){
                            continue;
                        }
                        $data_sn[]=[
                            'sn'=>$v,
                            'store_in'=>$info['id'],
                            'shop'=>$info['shop'],
                            'goods'=>$info['goods'],
                        ];
                    }
                    $m_goods_sn->insertAll($data_sn);
                }
                
            }
             
            //更新出库记录
            $time=time();
            $update_info=[
                'time'=>$time,
                'rtime'=>$time,
                'rid'=>$admin['id'],
                'rdsc'=>$rdsc,
                'status'=>$status,
            ];
            $where=[
                'id'=>$info['id'],
                'status'=>1
            ];
            
            $row=$this->where('id',$info['id'])->update($update_info); 
            if($row===1){
                //更新库存
                if($status==2){
                    //出库数量
                    $m_goods=new GoodsModel();
                    $update_info=[
                        'time'=>$time,
                    ];
                    $where=[
                        'id'=>$info['goods'],
                        'store_num'=>['egt',$info['num']],
                    ];
                    $row=$m_goods->where($where)->dec('store_num',$info['num'])->update($update_info);
                    if($row!==1){
                        $row='库存信息更新失败，请确保库存足够';
                    }
                }
            }else{
                $row='审核失败，请刷新重试';
            }
           
           
        }   
        flock($fp,LOCK_UN);
        fclose($fp);
        return $row;
            
    }
    
     
    /**
     * 还原已审核出出库
     * @param array $info
     * @param array $admin
     * @param string $rdsc
     * @return number|string
     */
    public function instore_back($info,$admin,$rdsc='还原已审核出库'){
        //统一文件锁
        $file = "log/store_lock.txt";
        $fp = fopen($file, "r");
        if (flock($fp, LOCK_EX )){
            //更新出库记录
            $time=time();
            $update_info=[
                'time'=>$time,
                'rtime'=>$time,
                'rid'=>$admin['id'],
                'rdsc'=>$rdsc,
                'status'=>1,
            ];
            $where=[
                'id'=>$info['id'],
                'status'=>['between',[2,3]]
            ];
            
            $row=$this->where('id',$info['id'])->update($update_info);
            if($row===1){
                //还原库存
                if($info['status']==2){
                    //出库数量
                    $m_goods=new GoodsModel();
                    $update_info=[
                        'time'=>$time,
                    ];
                    $row=$m_goods->where('id',$info['goods'])->inc('store_num',$info['num'])->update($update_info);
                    if($row!==1){
                        $row='库存信息更新失败，请刷新重试';
                    }
                }
            }else{
                $row='审核失败，请刷新重试';
            }
             
        }
        flock($fp,LOCK_UN);
        fclose($fp);
        
        return $row; 
    }
    
}

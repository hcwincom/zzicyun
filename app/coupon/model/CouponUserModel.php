<?php
 
namespace app\coupon\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandModel;
class CouponUserModel extends Model
{
     /**
      * 获取可用优惠券
      * $uid用户，$type人民币1美元2
      */
    public function get_list($uid,$type=3,$use_ok=1){
        $where=[
            'uid'=>$uid, 
            'status'=>2
        ];
        if($use_ok==1){
            $where['status_use']=1;
            $where['status_time']=2;
        }
        if($type==1){
            $where['money_type']=['in',[1,3]];
        }elseif($type==2){
            $where['money_type']=['in',[2,3]];
        }
        
        $list=$this->where($where)->column('');
        if($type==2 && !empty($list)){
            //获取美元汇率
            $rate=Db::name('shop')->where('id',1)->value('rate');
            //计算人民币对应的美元价值
            foreach($list as $k=>$v){
                //通用价格计算
                if($v['money_type']==3){
                    $list[$k]['money']=round($v['money']*$rate,2);
                    $list[$k]['money_min']=round($v['money_min']*$rate,2);
                }
               
            }
        }
         
        return $list;
    }
    /**
     * 获取可用优惠券
     * $uid用户，$type人民币1美元2
     */
    public function get_page($where,$data,$type=3,$page_row=10){
        if($type==1){
            $where['money_type']=['in',[1,3]];
        }elseif($type==2){
            $where['money_type']=['in',[2,3]];
        }
        $list=$this->where($where)->paginate($page_row);
        $page = $list->appends($data)->render();
        if($type==2 && !empty($list)){
            //获取美元汇率
            $rate=Db::name('shop')->where('id',1)->value('rate');
            
            foreach($list as $k=>$v){
                //通用价格计算
                if($v['money_type']==3){
                    $list[$k]['money']=round($v['money']*$rate,5);
                    $list[$k]['money_min']=round($v['money_min']*$rate,5);
                }
            }
        }
        
        
        return ['list'=>$list,'page'=>$page];
    }
    /**
     * 获取优惠券详情
     * $id优惠券id，$type人民币1美元2 
     */
    public function get_one($id,$type){
        $where=[ 
            'id'=>$id
        ];
        
        $info=$this->where($where)->find();
        if(empty($info)){
            return [];
        }
        $info=$this->getData();
        if($type==2 ){
            //获取美元汇率
            $rate=Db::name('shop')->where('id',1)->value('rate'); 
                //价格计算
            if($info['money_type']==3){
                $info['money']=round($info['money']*$rate,5);
                $info['money_min']=round($info['money_min']*$rate,5);
            } 
        }
        
        
        return $info;
    }
    /**
     * 领取优惠券
     */
    public function user_get($id,$uid){
        $time=time();
        $m_coupon=Db::name('coupon');
        $info=$m_coupon->where('id',$id)->find();
        if($info['status'] !=2){
            return '该优惠券不可领用';
        }
        if($info['count'] >0 && $info['count_use'] >= $info['count'] ){
            return '该优惠券已领完';
        }
        //检测是否已领过 
        if($info['num'] >0 ){
            $where=['uid'=>$uid,'coupon'=>$id,'status'=>2];
            $count=$this->where($where)->count();
            if($count >= $info['num']){
                return '该优惠券已超过领用次数，不能再领用';
            }
        }
        $data_add=[
            'status'=>2,
            'uid'=>$uid,
            'name'=>$info['name'],
            'dsc'=>$info['dsc'],
            'coupon'=>$info['id'],
            'money_type'=>$info['money_type'],
            'sort'=>$info['sort'],
            'money'=>$info['money'],
            'money_min'=>$info['money_min'],
            'aid'=>1,
            'rid'=>1,
            'atime'=>$time,
            'rtime'=>$time,
            'time'=>$time,
        ];
        
        $time0=strtotime(date('Y-m-d'),$time);
        if(empty($info['time1'])){
            $data_add['time1']=$time0;
            $data_add['time2']=$data_add['time1']+24*3600*$info['day']-1;
        }else{
            $data_add['time1']=$info['time1'];
            $data_add['time2']=$info['time2'];
        }
        $res=$this->insertGetId($data_add);
        $m_coupon->where('id',$id)->setInc('count_get',1);
        return $res;
    }
    /**
     * 领取优惠券
     */
    public function user_gets($data,$admin){
        $data_add=[];
        $time=time();
       
        if(empty($data['coupon']) || empty($data['uids'])){
           return ('优惠券和用户为必选');
        }
      
        $m_coupon=Db::name('coupon');
        $coupon=$m_coupon->where('id',$data['coupon'])->find();
        if($coupon['status'] !=2){
            return '该优惠券不可领用';
        }
        if($coupon['count'] >0 && $coupon['count_use'] >= $coupon['count'] ){
            return '该优惠券已领完';
        }
       
        //检测是否已领过，是否已超过领用数量
        if($coupon['num'] >0 ){
            $uids=[];
            $where=['coupon'=>$data['coupon'],'status'=>2,'uid'=>['in',$data['uids']]];
            $counts=$this->where($where)->group('uid')->column('uid,count(id)');
             
            foreach($data['uids'] as $v){
                if(empty($counts[$v]) || $counts[$v] < $coupon['num']){
                    $uids[]=$v;
                }
            }
            if(empty($uids)){
                return '所有选中用户都已超过领用次数，不能再领用';
            }
        }else{
            $uids=$data['uids'];
        }
        $data_add['status']=2;
        $data_add['name']=$data['name'];
        $data_add['dsc']=$data['dsc'];
        $data_add['coupon']=$data['coupon'];
        $data_add['aid']=$admin['id'];
        $data_add['atime']=$time;
        $data_add['rid']=$admin['id'];
        $data_add['rtime']=$time;
        $data_add['time']=$time;
      
        if(empty($data['name'])){
            $data_add['name']=$coupon['name'];
        }
        if(empty($data['dsc'])){
            $data_add['dsc']=$coupon['dsc'];
        }
        $data_add['sort']=$coupon['sort'];
        $data_add['type']=$coupon['type'];
        $data_add['money']=$coupon['money'];
        $data_add['money_min']=$coupon['money_min'];
        $data_add['money_type']=$coupon['money_type'];
        $data_add['sort']=$coupon['sort'];
        $time0=strtotime(date('Y-m-d'),$time);
        if(empty($coupon['time1'])){
            $data_add['time1']=$time0;
            $data_add['time2']=$data_add['time1']+24*3600*$coupon['day']-1;
            $data_add['status_time']=2;
        }else{
            $data_add['time1']=$coupon['time1'];
            $data_add['time2']=$coupon['time2'];
            if($data_add['time1']>=$data_add['time2'] || $time>=$data_add['time2']){
                return ('起止时间错误'); 
            } 
        }
        
        if($data_add['time1']>$time){
            $data_add['status_time']=1;
        }else{
            $data_add['status_time']=2;
        }
        $data_adds=[];
        foreach($data['uids'] as $v){
            $data_add['uid']=$v;
            $data_adds[]=$data_add;
        }
        
        $row=$this->insertAll($data_adds);
        $m_coupon->where('id',$data['coupon'])->setInc('count_get',count($uids));
        return $row;
    }
    /**
     * 获取用户优惠券数量
     * @param int $uid
     * @param number $status
     */
    public function get_count($uid,$status=1){
        $where=[
            'uid'=>$uid,
            'status_use'=>1,
        ];
        if($status==3){
            $where['status_time']=3;
        }else{
            $where['status_time']=['lt',3];
        }
         
        $count=$this->where($where)->count(); 
        return $count;
    }
    /**
     * 推荐用户优惠券
     * @param int $uid用户
     * @param float $money产品金额
     */
    public function get_recommend($uid,$money,$price_type){
        $where=[
            'uid'=>$uid,
            'status_use'=>1,
            'status_time'=>2,
            'money_min'=>['elt',$money]
        ];
        $rate=Db::name('shop')->where('id',1)->value('rate');
        if($price_type==1){
            $where['money_type']=['in',[1,3]];
            $where['money_min']=['elt',$money];
            $id=$this->where($where)->order('money desc')->value('id');
        }else{
            //美元情况复杂,先获取美元再获取通用,比较优惠金额
            $where['money_type']=['eq',2];
            $where['money_min']=['elt',$money];
            $id2=$this->where($where)->order('money desc')->find();
            $where['money_type']=['eq',3];
            $where['money_min']=['elt',$money*$rate];
            $id1=$this->where($where)->order('money desc')->find();
            if(empty($id2)){
                if(empty($id1)){
                    $id=0;
                }else{
                    $id=$id1['id'];
                }
            }else{
                if(empty($id1)){
                    $id=$id2['id'];
                }else{
                    $money1=$id1['money']*$rate;
                    $id=($money1>$id2['money'])?$id1['id']:$id2['id'];
                }
            }
        }
         
        return $id;
    }
}

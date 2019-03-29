<?php
 
namespace app\score\model;

use think\Model; 
use app\user\model\UserModel;
use app\user\model\UserAddressModel;

class GiftUserModel extends Model
{
     
    /**
     * 按where获取礼品
     */
    public function get_page($uid,$page_row=12){
        
        $list=[]; 
        $field='*';
        $list=$this->where('uid',$uid)->order('id desc')->paginate($page_row);
       
        // 获取分页显示
        $page = $list->render();
        return ['list'=>$list,'page'=>$page];
    }
    /**
     * 获取礼品兑换详情
     */
    public function get_one($id,$uid){
        $where=[
            'id'=>$id,
            'uid'=>$uid
        ];
        $info=$this->where($where)->find();
        
        return $info->getData();
    }
    
    public function gift_exchange($data,$uid){
        //礼品详情
        $m_gift=new GiftModel(); 
        $gift=$m_gift->get_one($data['id']);
        //用户数据
        $m_user=new UserModel();
        $user=$m_user->get_user($uid);
        //计算
        $count_score=$gift['score']*$data['num'];
        if($count_score>$user['score']){
            return '积分不足';
        }
        //扣除积分
        $m_score=new ScoreUserModel();
        $res=$m_score->score_do($uid,'gift',0,(0-$count_score));
        if(!($res>0)){
            return $res;
        }
        //获取地址详情
        $m_address=new UserAddressModel();
        $address=$m_address->get_one($uid,$data['address_id']);
        //添加兑换记录
        $data_add=[
            'uid'=>$uid,
            'gift'=>$gift['id'],
            'gift_name'=>$gift['name'],
            'gift_price'=>$gift['price'],
            'score'=>$gift['score'],
            'num'=>$data['num'],
            'score_count'=>$count_score,
            'time'=>time(),
            'accept_name'=>$address['accept_name'],
            'tel'=>$address['tel'],
            'country'=>$address['country'],
            'province'=>$address['province'],
            'city'=>$address['city'],
            'area'=>$address['area'],
            'address'=>$address['address'],
            'address_info'=>$address['address_info'], 
        ];
        $this->insert($data_add);
        return 1;
    }
}

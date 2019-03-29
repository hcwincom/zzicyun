<?php
 
namespace app\score\model;

use think\Model; 
use think\Db; 
class ScoreUserModel extends Model
{
     /**
      *积分变化
      */
    public function score_do($uid,$code,$money=0,$score=0,$aid=1){
        //先得到积分规则
        $where=[ 
            'code'=>$code, 
        ];
        $rule=Db::name('score_rule')->where($where)->find();
        if(empty($rule)){
            return 1;
        }
        $time=time();
        //有次数限制
        if($rule['number']>0){
           $where=[
               'uid'=>$uid,
               'pid'=>$rule['id']
           ];
           //每天限制次数
           if($rule['type']==2){
               $where['time']=['gt',strtotime(date('Y-m-d',$time))];
           }
           $count=$this->where($where)->count();
           //超过次数，不用继续了
           if($count>=$rule['number']){
               return 1;
           }
        }
        //没有指定积分则按默认的来
        $score_add=($score==0)?$rule['score']:$score; 
        if(!empty($money) && !empty($rule['rate'])){
            $score_add+=intval($money*$rule['rate']);
        }
        $data_add=[
            'uid'=>$uid,
            'pid'=>$rule['id'],
            'score'=>$score_add,
            'time'=>$time,
            'dsc'=>$rule['name'],
            'aid'=>$aid, 
        ];
        $this->insert($data_add);
        Db::name('user')->where('id',$uid)->setInc('score',$score_add);
        return 1;
    }
    /**
     * 积分记录
     */
    public function get_page($uid,$where,$data,$page_row=12){
        
         
        $list=$this->where($where)->order('id desc')->paginate($page_row);
        
        // 获取分页显示
        $page = $list->append($data)->render();
        return ['list'=>$list,'page'=>$page];
    }
    
}

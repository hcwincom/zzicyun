<?php

namespace app\user\model;

use think\Db;
use think\Model;

class UserBalanceModel extends Model
{
    /**
     *余额变化
     */
    public function balance_do($uid,$balance,$dsc='充值',$aid=1){
         
        $data_add=[
            'uid'=>$uid, 
            'balance'=>$balance,
            'time'=>time(),
            'dsc'=>$dsc,
            'aid'=>$aid,
        ];
        $this->insert($data_add);
        Db::name('user')->where('id',$uid)->setInc('balance',$balance);
        return 1;
    }
 
}

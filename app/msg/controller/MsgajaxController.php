<?php

namespace app\msg\controller;

use app\common\controller\AdminBase0Controller;
use think\Db;
 

class MsgajaxController extends AdminBase0Controller
{
    
    function user_search(){
        $data=$this->request->param();
        $types=config('user_search');
        $search_types=config('search_types');
        $where = [];
        $admin=$this->admin;
        /**搜索条件**/
        $data = $this->request->param();
        if($admin['shop']==1){
            if(!empty($data['shop']) && $data['shop']>0){
                $where['shop'] =  ['eq',$data['shop']];
            }
           
        }else{
            $where['shop'] =  ['eq',$admin['shop']];
        }
        
       if(!empty($data['user_rate'])){
           $where['user_rate'] = $data['user_rate'];
       }
       if(!empty($data['user_cate'])){
           $where['user_cate'] = $data['user_cate'];
       }
       if(!empty($data['user_type'])){
           $where['user_type'] = $data['user_type'];
       }
        $res=zz_search_param($types, $search_types, $data, $where);
       
        $users = Db::name('user')->where($where)->order('id asc')->column('id,user_login');
        
        $this->success('ok','',$users);
    }
     
}

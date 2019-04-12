<?php

namespace app\notice\controller;


use app\common\controller\AdminInfoController;
use think\Db;
use app\notice\model\NoticeCateModel;
use app\notice\model\NoticeConfValModel;
/**
 *  系统配置的多语言
 */
class AdminNoticeConfController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='系统配置信息';
        $this->table='notice_conf_val';
        $this->m=new NoticeConfValModel();
        
        //没有店铺区分
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'shop_types'=>'供应商类型',
            'pdf_cates'=>'产品文件类型',
            'store_sures'=>'库存类型',
            'is_rohs'=>'是否符合rohs标准',
            'user_rates'=>'用户等级',
            'user_cates'=>'用户类型', 
            'money_type'=>'币种', 
            'money_type3'=>'币种加通用', 
           
            
        ];
        
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        $this->assign('confs',$this->vals);
        
        
    }
    /**
     * 系统配置信息列表
     * @adminMenu(
     *     'name'   => '系统配置信息列表',
     *     'parent' => 'notice/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 3,
     *     'icon'   => '',
     *     'remark' => '系统配置信息列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
         
        return $this->fetch();
    }
    
     
    /**
     * 系统配置信息详情
     * @adminMenu(
     *     'name'   => '系统配置信息详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '系统配置信息详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $m=$this->m;
        $key=$this->request->param('id','');
        if(empty($key)){
            $this->error('数据不存在');
        }
        //配置信息
        $conf=config($key);
        if(empty($conf)){
            $this->error('数据不存在');
        }
        //所有语言
        $this->assign('lans', session('admin_lans'));
       //现有的配置语言值
        $list=$m->get_all_by_conf($key);
        $this->assign('list', $list);
        $this->assign('key', $key);
        $this->assign('conf', $conf);
        return $this->fetch();
    }
    
    /**
     * 系统配置信息编辑提交
     * @adminMenu(
     *     'name'   => '系统配置信息编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '系统配置信息编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        $data=$this->request->param();
       
        //配置信息
        $conf=config($data['id']);
        if(empty($conf)){
            $this->error('数据不存在');
        }
        //现有的配置语言值
        $m=$this->m;
        $list0=$m->get_all_by_conf($data['id']);
        $data_add=[];
        $data_update=[];
        //依次比较，不同直接更改，没有稍后添加
        foreach($conf as $k=>$v){
            foreach($data['val-'.$k] as $kk=>$vv){
                if(isset($list0[$k][$kk])){
                    if($list0[$k][$kk] != $vv){
                       $where=[
                           'conf'=>$data['id'],
                           'key'=>$k,
                           'lid'=>$kk, 
                       ];
                       $update=['val'=>$vv];
                       $m->where($where)->update($update);
                    }
                }else{
                    $data_add[]=[
                        'conf'=>$data['id'],
                        'key'=>$k,
                        'lid'=>$kk,
                        'val'=>$vv,
                    ];
                }
            }
        }
        if(!empty($data_add)){
            $m->insertAll($data_add);
        }
        $this->success('数据已更新');
    }
     
}

<?php
 
namespace app\shop\controller;

 
use app\common\controller\AdminInfoController; 
use think\Db; 
  
class AdminShopController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='加盟店铺';
        $this->table='shop';
        $this->m=Db::name('shop');
        //没有店铺区分
        $this->isshop=0;
        
        $this->base=['name'=>'str',
            'sort'=>'int',
            'dsc'=>'str',
            'type'=>'int',
            'is_review'=>'int',
            'address'=>'str',
            'code'=>'str',
            'logo'=>'str',
            
        ];
        $this->islan=1;
        $this->vals=[
            'name'=>'多语言名称',
            'char'=>'名称首字母',
            'dsc'=>'多语言说明',
            'pros'=>'产品线', 
            'brands'=>'主营产品', 
        ];
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        $this->assign('is_review',[1=>'二次审核',2=>'直接审核']);
    }
    /**
     * 加盟店铺列表
     * @adminMenu(
     *     'name'   => '加盟店铺列表',
     *     'parent' => 'shop/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 2,
     *     'icon'   => '',
     *     'remark' => '加盟店铺列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
         parent::index();
        return $this->fetch();
    }
     
   
    /**
     * 加盟店铺添加
     * @adminMenu(
     *     'name'   => '加盟店铺添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        return $this->fetch();  
        
    }
    /**
     * 加盟店铺添加do
     * @adminMenu(
     *     'name'   => '加盟店铺添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        $m=$this->m;
        $data=$this->request->param();
        $res=$this->param_check($data);
        if($res!=1){
            $this->error($res);
        }
        
        $url=url('index');
        
        $table=$this->table;
        $time=time();
        $admin=$this->admin;
        $fields=$this->base;
        //str,int,round1,round2,round3,round4,pic,file
        $data_add=[];
        //添加的信息
        foreach($fields as $k=>$v){
            if(isset($data[$k])){
                $data_add[$k]=$data[$k];
            }
        }
       
        //判断是否有店铺
        if($this->isshop){
            $data_add['shop']=$admin['shop'];
        }
        
        $data_add['status']=1;
        $data_add['aid']=$admin['id'];
        $data_add['atime']=$time;
        $data_add['time']=$time;
        
        $m->startTrans();
        $id=$m->insertGetId($data_add);
        //多语言
        if($this->islan){
            $vals=$this->vals;
            $data_vals=[];
            foreach($vals as $k=>$v){
                foreach($data['lan-'.$k] as $kk=>$vv){
                    if(isset($data_vals[$k.'-'.$kk])){
                        $data_vals[$k.'-'.$kk][$k]=$vv;
                    }else{
                        $data_vals[$k.'-'.$kk]=[
                            'pid'=>$id,
                            'lid'=>$kk,
                            $k=>$vv,
                        ];
                    }
                }
            }
            Db::name('notice_val')->insertAll($data_vals);
        }
        $path='upload/';
        $pathid='seller'.$id.'/';
        //新店铺要创建目录
        if(!is_dir($path.$pathid)){
            mkdir($path.$pathid);
        }
        //图片处理 
        if (!empty($data_add['logo']))
        { 
            $pathid='seller'.$id.'/logo/';
            $logo_new=zz_set_file($data_add['logo'],$pathid,config('pic_logo'));
            if($logo_new!=$data_add['logo']){
                $m->where('id',$id)->setField('logo',$logo_new);
            }
        }
         
        //记录操作记录
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'添加'.($this->flag).$id.'-'.$data['name'],
            'table'=>($this->table),
            'type'=>'add',
            'pid'=>$id,
            'link'=>url('edit',['id'=>$id]),
            'shop'=>$admin['shop'],
            
        ];
        zz_action($data_action,['department'=>$admin['department']]);
        
        $m->commit();
        //直接审核
        $rule='review';
        $res=$this->check_review($admin,$rule);
        if($res){
            $this->redirect($rule,['id'=>$id,'status'=>2]);
        }
        $this->success('添加成功',$url);
        
    }
    /**
     * 加盟店铺详情
     * @adminMenu(
     *     'name'   => '加盟店铺详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();  
        return $this->fetch();  
    }
    /**
     * 加盟店铺状态审核
     * @adminMenu(
     *     'name'   => '加盟店铺状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 加盟店铺状态批量同意
     * @adminMenu(
     *     'name'   => '加盟店铺状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 加盟店铺禁用
     * @adminMenu(
     *     'name'   => '信息状态禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '信息状态禁用',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        parent::ban();
    }
    /**
     * 加盟店铺信息状态恢复
     * @adminMenu(
     *     'name'   => '加盟店铺信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 加盟店铺编辑提交
     * @adminMenu(
     *     'name'   => '加盟店铺编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        $m=$this->m;
        $table=$this->table;
        $flag=$this->flag;
        $data=$this->request->param();
        $res=$this->param_check($data);
        if($res!=1){
            $this->error($res);
        }
        $info=$m->where('id',$data['id'])->find();
        if(empty($info)){
            $this->error('数据不存在');
        }
        $time=time();
        $admin=$this->admin;
        //其他店铺的审核判断
        if($admin['shop']!=1){
            if(empty($info['shop']) || $info['shop']!=$admin['shop']){
                $this->error('不能编辑其他店铺的信息');
            }
        }
        $update=[
            'pid'=>$info['id'],
            'aid'=>$admin['id'],
            'atime'=>$time,
            'table'=>$table,
            'url'=>url('edit_info','',false,false),
            'rstatus'=>1,
            'rid'=>0,
            'rtime'=>0,
            'shop'=>$admin['shop'],
        ];
        $update['adsc']=(empty($data['adsc']))?('修改了'.$flag.'信息'):$data['adsc'];
        $fields=$this->base;
        //str,int,round1,round2,round3,round4,pic,file
        $content=[];
        //图片处理
        $path='upload/';
        $pathid='seller'.$info['id'].'/';
        //新店铺要创建目录
        if(!is_dir($path.$pathid)){
            mkdir($path.$pathid);
        }
        if (!empty($data['logo']))
        { 
            $path='upload/';
            $pathid='seller'.$info['id'].'/logo/';
            $data['logo']=zz_set_file($data['logo'],$pathid,config('pic_logo'));
            
        }
        //检测改变了哪些字段
        //如果原信息和$data信息相同就未改变，不为空就记录，？null测试
        foreach($fields as $k=>$v){
            if(isset($data[$k])){
                if($info[$k]!=$data[$k]){
                    $content[$k]=$data[$k];
                }
            }
            
        }
        //多语言
        if($this->islan){
            //获取原多语言值
            $lans_info=Db::name($table.'_val')->where('pid',$info['id'])->column('*','lid');
            $vals=$this->vals;
            $data_vals=[];
            foreach($vals as $k=>$v){
                foreach($data['lan-'.$k] as $kk=>$vv){
                    if(!isset($lans_info[$kk][$k]) || $lans_info[$kk][$k] != $vv){
                        $data_vals[$kk][$k]=$vv;
                    }
                }
            }
            if(!empty($data_vals)){
                $content['lans']=$data_vals;
            }
        }
        if(empty($content)){
            $this->error('未修改');
        }
        //保存更改
        $m_edit=Db::name('edit');
        $m_edit->startTrans();
        $eid=$m_edit->insertGetId($update);
        if($eid>0){
            $data_content=[
                'eid'=>$eid,
                'content'=>json_encode($content),
            ];
            Db::name('edit_info')->insert($data_content);
        }else{
            $m_edit->rollback();
            $this->error('保存数据错误，请重试');
        }
        
        //记录操作记录
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'编辑了'.($this->flag).$info['id'].'-'.$info['name'],
            'table'=>($this->table),
            'type'=>'edit',
            'pid'=>$info['id'],
            'link'=>url('edit_info',['id'=>$eid]),
            'shop'=>$admin['shop'],
        ];
        
        zz_action($data_action,['department'=>$admin['department']]);
        
        $m_edit->commit();
        //判断是否直接审核
        $rule='edit_review';
        $res=$this->check_review($admin,$rule);
        if($res){
            $this->redirect($rule,['id'=>$eid,'rstatus'=>2,'rdsc'=>'直接审核']);
        }
        
        $this->success('已提交修改');
    }
    /**
     * 加盟店铺编辑列表
     * @adminMenu(
     *     'name'   => '加盟店铺编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 加盟店铺审核详情
     * @adminMenu(
     *     'name'   => '加盟店铺审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();  
    }
    /**
     * 加盟店铺信息编辑审核
     * @adminMenu(
     *     'name'   => '加盟店铺编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 加盟店铺编辑记录批量删除
     * @adminMenu(
     *     'name'   => '加盟店铺编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 加盟店铺
     * @adminMenu(
     *     'name'   => '加盟店铺',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '加盟店铺',
     *     'param'  => ''
     * )
     */
    public function del_all()
    { 
       if(empty($_POST['ids'])){
            $this->error('没有选择店铺');
       }
       $ids=$_POST['ids'];
       if(in_array(1,$ids) ){
           $this->error('总站不能修改');
       }
       $where=['shop'=>['in',$ids]];
       $user=Db::name('user')->where($where)->find();
       if(!empty($user)){
            $this->error('店铺'.$user['shop'].'下有用户，不能删除');
        }
      
       
        Db::name('goods')->where($where)->delete();
        Db::name('goods_label')->where($where)->delete();
        Db::name('goods_link')->where($where)->delete();
        Db::name('goods_sn')->where($where)->delete();
        Db::name('goods_label')->where($where)->delete();
        Db::name('msg')->where($where)->delete();
        Db::name('action')->where($where)->delete();
        
        Db::name('store_goods')->where($where)->delete();
        Db::name('store_goods_history')->where($where)->delete();
        Db::name('store_in')->where($where)->delete();
        Db::name('store_box')->where($where)->delete();
        Db::name('store_floor')->where($where)->delete();
        Db::name('store_shelf')->where($where)->delete();
        Db::name('store')->where($where)->delete();
         
        Db::name('attendance_day')->where($where)->delete();
        Db::name('attendance_apply')->where($where)->delete();
        Db::name('attendance_date')->where($where)->delete();
        Db::name('attendance_rule')->where($where)->delete();
        
        //有相关垃圾未删除，如event_uid ,role_user
        Db::name('event')->where($where)->delete();
        Db::name('user')->where($where)->delete();
        Db::name('order')->where($where)->delete();
        Db::name('ordersup')->where($where)->delete();
        Db::name('orderq')->where($where)->delete();
        Db::name('orderback')->where($where)->delete();
        Db::name('custom')->where($where)->delete();
        Db::name('supplier')->where($where)->delete();
        Db::name('edit')->where($where)->delete();
        Db::name('freightpays')->where($where)->delete();
        Db::name('expressarea')->where($where)->delete();
        $rows=Db::name('shop')->where('id','in',$ids)->delete();
        $this->success('成功删除店铺');
         
    }
    public function cates($type=3){
        parent::cates($type);
        $this->assign('shop_types',config('shop_types'));
        $this->assign('chars',config('chars'));
    }
    
}

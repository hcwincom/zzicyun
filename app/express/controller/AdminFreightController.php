<?php
 
namespace app\express\controller;

 
use app\common\controller\AdminInfoController; 
use think\Db; 
use app\express\model\FreightAreaModel;
  
class AdminFreightController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='合作快递';
        $this->table='freight';
        $this->m=Db::name('freight');
      
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','express'=>'int',
            'type1'=>'int','type2'=>'int','type3'=>'int' 
        ];
        
        $this->isshop=0;
        $this->islan=0;
        $this->vals=[
            'name'=>'多语言名称',
        ];
      
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
    }
    /**
     * 店铺合作快递列表
     * @adminMenu(
     *     'name'   => '店铺合作快递列表',
     *     'parent' => 'express/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
         parent::index();
        return $this->fetch();
    }
     
   
    /**
     * 店铺合作快递添加
     *  @adminMenu(
     *     'name'   => '店铺合作快递添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        $this->assign('fees',[]);
        return $this->fetch();  
        
    }
    /**
     * 店铺合作快递添加do
     *  @adminMenu(
     *     'name'   => '店铺合作快递添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 店铺合作快递详情
     *  @adminMenu(
     *     'name'   => '店铺合作快递详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();  
      
        return $this->fetch();  
    }
    /**
     * 店铺合作快递状态审核
     *  @adminMenu(
     *     'name'   => '店铺合作快递状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 店铺合作快递状态批量同意
     *  @adminMenu(
     *     'name'   => '店铺合作快递状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 店铺合作快递禁用
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
     * 店铺合作快递信息状态恢复
     * @adminMenu(
     *     'name'   => '店铺合作快递信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 店铺合作快递编辑提交
     * @adminMenu(
     *     'name'   => '店铺合作快递编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 店铺合作快递编辑列表
     * @adminMenu(
     *     'name'   => '店铺合作快递编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        
        return $this->fetch();  
    }
    
    /**
     * 店铺合作快递审核详情
     *  @adminMenu(
     *     'name'   => '店铺合作快递审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();  
    }
    /**
     * 店铺合作快递信息编辑审核
     *  @adminMenu(
     *     'name'   => '店铺合作快递编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 店铺合作快递编辑记录批量删除
     *  @adminMenu(
     *     'name'   => '店铺合作快递编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 店铺合作快递批量删除
     * @adminMenu(
     *     'name'   => '店铺合作快递批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '店铺合作快递批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
         
        parent::del_all();
    }
   
    
    /**
     * 分类等关联信息
     *   */
    public function cates($type=3){
        parent::cates($type);
        //关联快递
        $where=[
            'status'=>2,
        ];
        $expresses=Db::name('express')->order('sort asc')->where($where)->column('id,name');
        $this->assign('expresses',$expresses);
        $this->assign('freight_types',config('freight_type'));
        //地区选择 
        $where=[
            'status'=>2,
        ];
        $areas=Db::name('area_custom')->order('sort asc')->where($where)->column('id,name');
        $this->assign('areas',$areas);
         
    }
     public function param_check(&$data){
         
         $data['type1']=empty($data['type1'])?2:1;
         $data['type2']=empty($data['type2'])?2:1;
         $data['type3']=empty($data['type3'])?2:1;
         if(!empty($data['price1'])){
             foreach($data['price1'] as $k=>$v){
                 $data['price1'][$k]=round($v,2);
                 $data['price2'][$k]=round($data['price2'][$k],2);
                 $data['weight1'][$k]=round($data['weight1'][$k],2);
                 $data['weight2'][$k]=round($data['weight2'][$k],2);
                 $data['weight0'][$k]=round($data['weight0'][$k],2);
                 $data['price0'][$k]=round($data['price0'][$k],2);
                 $data['size_rate'][$k]=intval($data['size_rate'][$k]);
             }
         }
         
         return 1;
     }
     /*额外添加的信息，一般是图片文件 */
     public function add_do_after($id,$data){
         
         if(empty($data['price1'])){
             return 1;
         }
         $data_add=[];
         foreach($data['price1'] as $k=>$v){
             $data_add[$k]=[
                 'area'=>$k,
                 'freight'=>$id,
                 'price1'=>$v,
                 'price2'=>$data['price2'][$k],
                 'weight1'=>$data['weight1'][$k],
                 'weight2'=>$data['weight2'][$k],
                 'price0'=>$data['price0'][$k],
                 'weight0'=>$data['weight0'][$k],
                 'size_rate'=>$data['size_rate'][$k],
             ];
         }
         $m_fr=new FreightAreaModel();
         $m_fr->insertAll($data_add);
         
         return 1;
     }
     /* 查看详情 */
     public function edit_after($info){
        
         $m_fr=new FreightAreaModel();
         $fees=$m_fr->get_all($info['id']);
         $this->assign('fees',$fees);
         
     }
      
     /* 删除前 */
     public function del_after($ids){
         $where=[
             'freight'=>['in',$ids]
         ];
         $m_fr=new FreightAreaModel();
         $m_fr->where($where)->delete(); 
         return 1;
     }
     /* 编辑中的处理 */
     public function edit_do_before(&$content,&$data){
         $m_fr=new FreightAreaModel();
         $fees=$m_fr->get_all($data['id']);
         if(empty($data['price1'])){
             $data['price1']=[];
         }
         $fee_add=[];
         $fee_del=[];
         $fee_edit=[];
         //比较修改和新增
         foreach($data['price1'] as $k=>$v){
             if(isset($fees[$k])){
                 if($fees[$k]['price1'] != $v){
                     $fee_edit[$k]['price1']=$v;
                 }
                 if($fees[$k]['price2'] != $data['price2'][$k]){
                     $fee_edit[$k]['price2']=$data['price2'][$k];
                 }
                 if($fees[$k]['weight1'] != $data['weight1'][$k]){
                     $fee_edit[$k]['weight1']=$data['weight1'][$k];
                 }
                 if($fees[$k]['weight2'] != $data['weight2'][$k]){
                     $fee_edit[$k]['weight2']=$data['weight2'][$k];
                 }
                 if($fees[$k]['weight0'] != $data['weight0'][$k]){
                     $fee_edit[$k]['weight0']=$data['weight0'][$k];
                 }
                 if($fees[$k]['price0'] != $data['price0'][$k]){
                     $fee_edit[$k]['price0']=$data['price0'][$k];
                 }
                 if($fees[$k]['size_rate'] != $data['size_rate'][$k]){
                     $fee_edit[$k]['size_rate']=$data['size_rate'][$k];
                 }
                  
             }else{
                 $fee_add[$k]=[
                     'area'=>$k,
                     'freight'=>$data['id'],
                     'price1'=>$v,
                     'price2'=>$data['price2'][$k],
                     'weight1'=>$data['weight1'][$k],
                     'weight2'=>$data['weight2'][$k],
                     'weight0'=>$data['weight0'][$k],
                     'price0'=>$data['price0'][$k],
                     'size_rate'=>$data['size_rate'][$k], 
                 ];
             }
             
         }
         //比较是否有删除
         foreach($fees as $k=>$v){
             if(!isset($data['price1'][$k])){
                 $fee_del[$k]=$k;
             }
         }
         if(!empty($fee_add)){
             $content['fee_add']=$fee_add;
         }
         if(!empty($fee_del)){
             $content['fee_del']=$fee_del;
         }
         if(!empty($fee_edit)){
             $content['fee_edit']=$fee_edit;
         }
         
         return 1;
         
     }
     /* 编辑详情 */
     public function edit_info_after($info,$change){
         $m_fr=new FreightAreaModel();
         $fees=$m_fr->get_all($info['id']);
         $this->assign('fees',$fees);
          
     }
     /* 编辑审核前的操作 */
     public function edit_review_before($info1){
         //得到修改的字段
         $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
         $change=json_decode($change,true);
         $m_fr=new FreightAreaModel();
         //删除
         if(isset($change['fee_del'])){
             $where=[
                 'freight'=>$info1['pid'],
                 'area'=>['in',$change['fee_del']]
             ];
             $m_fr->where($where)->delete();
             unset($change['fee_del']);
         }
        //修改
         if(isset($change['fee_edit'])){
             
             foreach($change['fee_edit'] as $k=>$v){
                 $where=[
                     'freight'=>$info1['pid'],
                     'area'=>$k
                 ];
                 $m_fr->where($where)->update($v);
                 unset($change['fee_edit']);
             }
             unset($change['fee_del']);
         }
         //新增
         if(isset($change['fee_add'])){
             //增加前要先删除
             $where=[
                 'freight'=>$info1['pid'],
                 'area'=>['in',array_keys($change['fee_add'])]
             ];
             $m_fr->where($where)->delete();
             $m_fr->insertAll($change['fee_add']);
             
             unset($change['fee_add']);
         }
         return $change;
     }
     
}

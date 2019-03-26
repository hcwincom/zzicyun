<?php
 
namespace app\express\controller;

 
use app\common\controller\AdminInfoController; 
use think\Db; 
  
class AdminAreaCustomController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='快递区域';
        $this->table='area_custom';
        $this->m=Db::name('area_custom');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str'];
        
        $this->assign('flag',$this->flag);
        $this->assign('table',$this->table);
        
    }
    /**
     * 自定义快递区域列表
     * @adminMenu(
     *     'name'   => '自定义快递区域列表',
     *     'parent' => 'express/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 2,
     *     'icon'   => '',
     *     'remark' => '自定义快递区域列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
         parent::index();
        return $this->fetch();
    }
     
   
    /**
     * 快递区域添加
     * @adminMenu(
     *     'name'   => '快递区域添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
       parent::add();
       $this->assign('citys',[]);
        return $this->fetch();  
        
    }
    /**
     * 快递区域添加do
     * @adminMenu(
     *     'name'   => '快递区域添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
        
    }
    /**
     * 快递区域详情
     * @adminMenu(
     *     'name'   => '快递区域详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
         parent::edit();
        return $this->fetch();  
    }
    /**
     * 快递区域状态审核
     * @adminMenu(
     *     'name'   => '快递区域状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 快递区域状态批量同意
     * @adminMenu(
     *     'name'   => '快递区域状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 快递区域禁用
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
     * 快递区域信息状态恢复
     * @adminMenu(
     *     'name'   => '快递区域信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 快递区域编辑提交
     * @adminMenu(
     *     'name'   => '快递区域编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 快递区域编辑列表
     * @adminMenu(
     *     'name'   => '快递区域编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 快递区域审核详情
     * @adminMenu(
     *     'name'   => '快递区域审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();  
    }
    /**
     * 快递区域信息编辑审核
     * @adminMenu(
     *     'name'   => '快递区域编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 快递区域编辑记录批量删除
     * @adminMenu(
     *     'name'   => '快递区域编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    
    /**
     * 快递区域批量删除
     * @adminMenu(
     *     'name'   => '快递区域批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '快递区域批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    { 
        parent::del_all();
    }
    public function cates($type=3){
        parent::cates($type);
        $where=['status'=>2,'fid'=>1];
        $city1s=Db::name('area')
        ->where($where)
        ->order('sort asc')
        ->column('id,name');
        $this->assign('city1s',$city1s);
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
         
        if(empty($data['citys'])){
            return 1; 
        }
        $data_add=[];
        foreach($data['citys'] as $v){
            $data_add[]=[
                'area'=>$id,
                'city'=>$v
            ];
        }
        Db::name('area_city')->insertAll($data_add);
       
        return 1;
    }
    /* 查看详情 */
    public function edit_after($info){
        //获取关联的城市
        $city_ids=Db::name('area_city')->where('area',$info['id'])->column('id','city');
        $this->assign('city_ids',$city_ids);
       //city做件名是为了比较，要重新得到citys
        $city_ids=array_keys($city_ids);
        $fids=[];
        $citys=[];
       
        if(!empty($city_ids)){ 
       
            $m_area=Db::name('area');
            //得到选中地区
            $fids=$m_area
            ->where('id','in',$city_ids)
            ->order('sort asc')
            ->column('id,fid');
        
            //获取有数据的省 
            $where=[
                'status'=>2,
                'fid'=>['in',array_unique($fids)]
            ];
            $citys0=$m_area->where($where)->column('id,name,fid');
            //按省分组 
            foreach($citys0 as $k=>$v){
                $citys[$v['fid']][$k]=$v['name'];
            }
         }
       
        $this->assign('citys',$citys);
       
    }
    
    /* 删除前 */
    public function del_before($ids){
        return 1;
    }
    /* 删除前 */
    public function del_after($ids){
        $where=[
            'area'=>['in',$ids]
        ];
        Db::name('area_city')->where($where)->delete();
       
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        if(empty($data['citys'])){
            $data['citys']=[];
        }
        
        $m_citys=Db::name('area_city');
        //获取已关联城市s
        $citys0=$m_citys->where('area',$data['id'])->column('city');
        $citys=[];
        foreach($citys0 as $v){
            $citys[$v]=$v;
        }
        //都没有关联城市
        if(empty($data['citys']) && empty($citys)){
            return 1;
        }
        $data_del=array_diff_assoc($citys,$data['citys']);
        $data_add=array_diff_assoc($data['citys'],$citys);
        $content['city_del']=$data_del;
        $content['city_add']=$data_add;
        
        return 1;
        
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
        //获取关联的城市
        $city_ids=Db::name('area_city')->where('area',$info['id'])->column('id','city');
        $this->assign('city_ids',$city_ids);
        $city_ids=array_keys($city_ids);
        $fids=[];
        $citys=[];
        if(!empty($change['city_add'])){
            $city_ids=array_merge($city_ids,$change['city_add']);
        }
        if(!empty($city_ids)){
            
            $m_area=Db::name('area');
            //得到选中地区
            $fids=$m_area
            ->where('id','in',$city_ids)
            ->order('sort asc')
            ->column('id,fid');
           
           
            //获取有数据的省
            $where=[
                'status'=>2,
                'fid'=>['in',array_unique($fids)]
            ];
            $citys0=$m_area->where($where)->column('id,name,fid');
            //按省分组
            foreach($citys0 as $k=>$v){
                $citys[$v['fid']][$k]=$v['name'];
            }
        } 
        $this->assign('citys',$citys);
        
        
    }
    /* 编辑审核前的操作 */
    public function edit_review_before($info1){
        //得到修改的字段
        $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
        $change=json_decode($change,true);
        $m_citys=Db::name('area_city');
        if(!empty($change['city_del'])){
            $where=[
                'area'=>$info1['pid'],
                'city'=>['in',$change['city_del']]
            ];
            $m_citys->where($where)->delete();
        }
        unset($change['city_del']);
        if(!empty($change['city_add'])){
            $add=[];
            foreach($change['city_add'] as $v){
                $add[]=[
                    'area'=>$info1['pid'],
                    'city'=>$v,
                ];
            }
            
            $m_citys->insertAll($add);
        }
        unset($change['city_add']);
        return $change;
    }
    
}

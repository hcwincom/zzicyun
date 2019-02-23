<?php

namespace app\goods\controller;


use think\Db; 
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsBrandModel;
/* 产品品牌的添加 */
class AdminGoodsBrandController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->flag='产品品牌';
        $this->table='goods_brand';
        $this->m=new GoodsBrandModel();
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','cid'=>'int','pic'=>'str'];
        
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'char'=>'名称首字母',
            'name'=>'多语言名称', 
            'dsc'=>'简介',
            
        ];
        $this->assign('flag','产品品牌');
        
    }
    /**
     * 产品品牌列表
     * @adminMenu(
     *     'name'   => '产品品牌列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 80,
     *     'icon'   => '',
     *     'remark' => '产品品牌列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
        $table=$this->table;
        $m=$this->m;
        $admin=$this->admin;
        
        $data=$this->request->param();
        $where=[];
        //店铺,分店只能看到自己的数据，总店可以选择店铺
        if($this->isshop){
            zz_shop($admin, $data, $where);
        }
        
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        //状态
        if(empty($data['fid'])){
            $data['fid']=0;
        }else{
            $arr=explode('-',$data['fid']);
            $data['fid']=$arr[0];
            $rate=$arr[1];
            if($rate==2){
                $where['fid']=['eq',$data['fid']];
            }else{
                $fids=$m->where('fid',$data['fid'])->column('id');
                $where['fid']=['in',$fids];
            }
        }
        
        //添加人
        if(empty($data['aid'])){
            $data['aid']=0;
        }else{
            $where['aid']=['eq',$data['aid']];
        }
        //审核人
        if(empty($data['rid'])){
            $data['rid']=0;
        }else{
            $where['rid']=['eq',$data['rid']];
        }
        
        //类型
        if(empty($data['type'])){
            $data['type']=0;
        }else{
            $where['type']=['eq',$data['type']];
        }
        //查询字段
        $types=$this->search;
        $search_types=config('search_types');
        zz_search_param($types,$search_types,$data,$where);
        
        
        //时间类别
        $times=config('pro_time_search');
        zz_search_time($times,$data,$where);
        
        $list=$m
        ->where($where)
        ->order('status asc,sort asc,time desc')
        ->paginate();
        
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $this->assign('page',$page);
        $this->assign('list',$list);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
        
        return $this->fetch();
    }
    
    
    /**
     * 产品品牌添加
     * @adminMenu(
     *     'name'   => '产品品牌添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
       
        return $this->fetch();
    }
    /**
     * 产品品牌添加do
     * @adminMenu(
     *     'name'   => '产品品牌添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品品牌详情
     * @adminMenu(
     *     'name'   => '产品品牌详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
      
        return $this->fetch();
    }
    /**
     * 产品品牌状态审核
     * @adminMenu(
     *     'name'   => '产品品牌状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品品牌状态批量同意
     * @adminMenu(
     *     'name'   => '产品品牌状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 产品品牌禁用
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
     * 产品品牌信息状态恢复
     * @adminMenu(
     *     'name'   => '产品品牌信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 产品品牌编辑提交
     * @adminMenu(
     *     'name'   => '产品品牌编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 产品品牌编辑列表
     * @adminMenu(
     *     'name'   => '产品品牌编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();
    }
    
    /**
     * 产品品牌审核详情
     * @adminMenu(
     *     'name'   => '产品品牌审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 产品品牌信息编辑审核
     * @adminMenu(
     *     'name'   => '产品品牌编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 产品品牌编辑记录批量删除
     * @adminMenu(
     *     'name'   => '产品品牌编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 产品品牌批量删除
     * @adminMenu(
     *     'name'   => '产品品牌批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品品牌批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
        
        parent::del_all();
        
    }
    
    public function cates($type=3){
        parent::cates($type);
        $brand_cates=Db::name('goods_brand_cate')->where('status',2)->order('sort asc')->column('id,name');
        $this->assign('brand_cates',$brand_cates);
       
        $pic_conf=config('pic_goods_brand');
        $this->assign('pic_conf',$pic_conf);
        $this->assign('chars',config('chars'));
        
    }
    /*删除前检查  */
    public function del_before($ids){
        $m=$this->m;
        
        //检查是否有产品
        $where=['brand'=>['in',$ids]];
        $tmp=Db::name('goods')->where($where)->find();
        if(!empty($tmp)){
            return ('品牌'.$tmp['brand'].'下有产品'.$tmp['name']);
        }
        return 1;
    }
    /**
     * 参数值过滤
     *   */
    public function param_check(&$data){
        $res=parent::param_check($data);
        if(!($res>0)){
            return $res;
        }
       
        //判断是否级别错误
        if(!empty($data['id'])){
            $m=$this->m;
            
            $table=$this->table;
            $path1=$table.'/'.$data['id'].'/';
            $pic_conf=config('pic_'.$table);
            $data['pic']=zz_set_file($data['pic'],$path1,$pic_conf); 
        }
        
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        
        $table=$this->table;
        $path1=$table.'/'.$id.'/';
        $data_update=[];
        
        $pic_conf=config('pic_'.$table);
        $data_update['pic']=zz_set_file($data['pic'],$path1,$pic_conf);
        
        $m=$this->m;
        $m->where('id',$id)->update($data_update);
        
        return 1;
    }
   
    
}

<?php
 
namespace app\goods\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
/* 产品分类的添加 */
class AdminGoodsCateController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='产品分类';
        $this->table='goods_cate';
        $this->m=new GoodsCateModel();
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','fid'=>'int','rate'=>'int','pic'=>'str'];
         
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'val'=>'多语言名称'
        ];
        $this->assign('flag','产品分类');
         
    }
    /**
     * 产品分类列表
     * @adminMenu(
     *     'name'   => '产品分类列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 20,
     *     'icon'   => '',
     *     'remark' => '产品分类列表',
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
        
        //父类cid1,cid2
        $cid1=0;
        $cid2=0;
        if(!empty($data['cid1'])){ 
           
            $cid1=$data['cid1'];
            if(empty($data['cid2'])){
                $fids=$m->where('fid',$cid1)->column('id');
                $fids[]=$cid1;
                $where['fid']=['in',$fids];
            }else{
                $cid2=$data['cid2']; 
                $where['fid']=['eq',$cid2];
            } 
          
            
        } 
        $this->assign('cid1',$cid1); 
        $this->assign('cid2',$cid2); 
     
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
        ->order('status asc,rate asc,sort asc,time desc')
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
     * 产品分类树
     * @adminMenu(
     *     'name'   => '产品分类树',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类树',
     *     'param'  => ''
     * )
     */
    public function tree()
    { 
       
        $m=$this->m;
        $list=$m->all_cates(1,1,'p.*,val.val',[]);
        
        $this->assign('list',$list); 
        
        return $this->fetch();
    }
   
    /**
     * 产品分类添加
     * @adminMenu(
     *     'name'   => '产品分类添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        $fid=$this->request->param('fid');
        $this->assign('fid',$fid);
        
        return $this->fetch();
    }
    /**
     * 产品分类添加do
     * @adminMenu(
     *     'name'   => '产品分类添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品分类详情
     * @adminMenu(
     *     'name'   => '产品分类详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品分类状态审核
     * @adminMenu(
     *     'name'   => '产品分类状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品分类状态批量同意
     * @adminMenu(
     *     'name'   => '产品分类状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 产品分类禁用
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
     * 产品分类信息状态恢复
     * @adminMenu(
     *     'name'   => '产品分类信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 产品分类编辑提交
     * @adminMenu(
     *     'name'   => '产品分类编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 产品分类编辑列表
     * @adminMenu(
     *     'name'   => '产品分类编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 产品分类审核详情
     * @adminMenu(
     *     'name'   => '产品分类审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 产品分类信息编辑审核
     * @adminMenu(
     *     'name'   => '产品分类编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 产品分类编辑记录批量删除
     * @adminMenu(
     *     'name'   => '产品分类编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 产品分类批量删除
     * @adminMenu(
     *     'name'   => '产品分类批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品分类批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
         
        parent::del_all();
        
    }
    
    public function cates($type=3){
        parent::cates($type);
        $m=$this->m;
        $where=[
           'status'=>2,
           'rate'=>['lt',3],
        ];
        $cates=$m->all_cates1($where);
        $this->assign('cates',$cates);
        $this->assign('cate_rates',[1=>'一级分类',2=>'二级分类',3=>'三级分类']);
        $pic_conf=config('pic_goods_cate');
        $this->assign('pic_conf',$pic_conf);
        
    }
    /*删除前检查  */
    public function del_before($ids){
        $m=$this->m;
        //先检查是否有子类
        $where=['fid'=>['in',$ids]];
        $tmp=$m->where($where)->find();
        if(!empty($tmp)){
           return ('分类'.$tmp['fid'].'下有子类'.$tmp['name']);
        }
        //检查是否有产品
        $where=['cid'=>['in',$ids]];
        $tmp=Db::name('goods')->where($where)->find();
        if(!empty($tmp)){
            return ('分类'.$tmp['cid'].'下有产品'.$tmp['name']);
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
        //得到级别
        if(empty($data['cid2'])){
            if(empty($data['cid1'])){
                $data['fid']=0;
                $data['rate']=1;
            }else{
                $data['fid']=$data['cid1'];
                $data['rate']=2;
            }
        }else{
            $data['fid']=$data['cid2'];
            $data['rate']=3;
        }
        //判断是否级别错误
        if(!empty($data['id'])){
            $m=$this->m;
            $rate=$m->where('id',$data['id'])->value('rate');
            if($rate!=$data['rate']){
                return '分类级别不可更改';
            }
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
    /* 查看详情 */
    public function edit_after($info){
        $cid1=0;
        $cid2=0;          
        switch($info['rate']){
            case 1:
                $cid1=0;
                break;
            case 2:
                $cid1=$info['fid'];
                break;
            case 3:
                $cid2=$info['fid'];
                $m=$this->m;
                $cid1=$m->where('id',$info['fid'])->value('fid');
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2); 
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
        $cid1=0;
        $cid2=0;
        $m=$this->m;
        switch($info['rate']){
            case 1:
                $cid1=0;
                break;
            case 2:
                $cid1=$info['fid'];
                break;
            case 3:
                $cid2=$info['fid']; 
                $cid1=$m->where('id',$info['fid'])->value('fid');
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2);
        if(isset($change['fid'])){
            $fname='';
            switch($info['rate']){ 
                case 2:
                    $fname=$m->where('id',$change['fid'])->value('name');
                    break;
                case 3:
                    $fname=$m
                    ->alias('c2')
                    ->join('cmf_goods_cate c1','c1.id=c2.fid')
                    ->where('c2.id',$change['fid'])
                    ->value('concat(c1.name,"-",c2.name)');
            }
            $this->assign('fname',$fname);
        }
    }
    /* 编辑审核前的操作,处理父级更改的产品数量统计  */
    public function edit_review_before($info1){
        //得到修改的字段
        $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
        $change=json_decode($change,true);
        if(isset($change['fid'])){
            $m=$this->m;
            $info=$m->where('id',$info1['pid'])->find();
            //获取原信息后就更新fid
            $m->where('id',$info1['pid'])->update(['fid'=>$change['fid']]);
            switch($info['rate']){
                case 2:
                    //原父级
                    $cid1=$info['fid']; 
                    $nums1=$this->where('fid',$cid1)->sum('num');
                    if(empty($nums1)){
                        $nums1=0;
                    }
                    $this->where('id',$cid1)->setField('num',$nums1);
                     
                   //新的父级
                    $cid1=$change['fid']; 
                    $nums1=$this->where('fid',$cid1)->sum('num');
                    if(empty($nums1)){
                        $nums1=0;
                    }
                    $this->where('id',$cid1)->setField('num',$nums1);
                   
                    break;
                case 3:
                    //原父级
                    $cid2=$info['fid'];
                    $nums2=$this->where('fid',$cid2)->sum('num');
                    if(empty($nums2)){
                        $nums2=0;
                    }
                    $this->where('id',$cid2)->setField('num',$nums2);
                    //获取父级，计算产品数
                    $cid1=$this->where('id',$cid2)->value('fid');
                    if(!empty($cid1)){
                        $nums1=$this->where('fid',$cid1)->sum('num');
                        if(empty($nums1)){
                            $nums1=0;
                        }
                        $this->where('id',$cid1)->setField('num',$nums1);
                    }
                    //现有的
                    //新的父级
                    $cid2=$change['fid'];
                    $nums2=$this->where('fid',$cid2)->sum('num');
                    if(empty($nums2)){
                        $nums2=0;
                    }
                    $this->where('id',$cid2)->setField('num',$nums2);
                    //获取父级，计算产品数
                    $cid1=$this->where('id',$cid2)->value('fid');
                    if(!empty($cid1)){
                        $nums1=$this->where('fid',$cid1)->sum('num');
                        if(empty($nums1)){
                            $nums1=0;
                        }
                        $this->where('id',$cid1)->setField('num',$nums1);
                    }
                    break;
            }
        }
        return $change;
    }
    
   
}

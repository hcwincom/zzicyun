<?php
 
namespace app\goods\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsModel;
use app\goods\model\GoodsParamModel;
use app\goods\model\GoodsPriceModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsFileModel;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Border;
/* 产品的添加 */
class AdminGoodsController extends AdminInfoController
{
    private $pdf_cate;
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='产品';
        $this->table='goods';
        $this->m=new GoodsModel();
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str','cid'=>'int','num_min'=>'int','brand'=>'int',
            'num_times'=>'int','price1'=>'round5','store_code'=>'str','pic'=>'str','pic0'=>'str','code'=>'str',
            'goods_time1'=>'int','goods_time2'=>'int','store_sure'=>'int','shop_type'=>'int','is_rohs'=>'int'
        ];
         
        $this->isshop=1;
        $this->islan=1;
        $this->vals=[
            'name'=>'多语言名称',
            'dsc'=>'多语言说明',
            'production_code'=>'生产批次',
            'production_factory'=>'制造商'
        ];
        $this->pdf_cate=config('pdf_cates');
        $this->assign('flag','产品');
         
    }
    /**
     * 产品列表
     * @adminMenu(
     *     'name'   => '产品列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品列表',
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
        $cid3=0;
        if(!empty($data['cid1'])){
            $m_cate=new GoodsCateModel();
            $cid1=$data['cid1'];
            if(empty($data['cid2'])){
                $cids=$m_cate->get_cids_by_fid($cid1); 
                $where['cid']=['in',$cids];
            }else{
                $cid2=$data['cid2'];
                if(empty($data['cid3'])){
                    $cids=$m_cate->get_cids_by_fid($cid2);
                    $where['cid']=['in',$cids];
                }else{
                    $cid3=$data['cid3'];
                    $where['cid']=['eq',$cid3];
                } 
            }
           
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2); 
        $this->assign('cid3',$cid3); 
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
     * 产品添加
     * @adminMenu(
     *     'name'   => '产品添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        
        $this->assign('prices',null);
        $this->assign('pdfs',null);
        
        return $this->fetch();
    }
    /**
     * 产品添加do
     * @adminMenu(
     *     'name'   => '产品添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 产品详情
     * @adminMenu(
     *     'name'   => '产品详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 产品状态审核
     * @adminMenu(
     *     'name'   => '产品状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 产品状态批量同意
     * @adminMenu(
     *     'name'   => '产品状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 产品禁用
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
     * 产品信息状态恢复
     * @adminMenu(
     *     'name'   => '产品信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 产品编辑提交
     * @adminMenu(
     *     'name'   => '产品编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 产品编辑列表
     * @adminMenu(
     *     'name'   => '产品编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 产品审核详情
     * @adminMenu(
     *     'name'   => '产品审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 产品信息编辑审核
     * @adminMenu(
     *     'name'   => '产品编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 产品编辑记录批量删除
     * @adminMenu(
     *     'name'   => '产品编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 产品批量删除
     * @adminMenu(
     *     'name'   => '产品批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
         
        parent::del_all();
        
    }
    
    public function cates($type=3){
        parent::cates($type);
        $m_cate=new GoodsCateModel();
        $where=[
           'status'=>2, 
        ];
        $cates=$m_cate->all_cates1($where);
        $this->assign('cates',$cates);
        
        $pic_conf=config('pic_goods');
        $this->assign('pic_conf',$pic_conf);
        $m_brand=new GoodsBrandModel();
        $brands=$m_brand->get_all();
        $this->assign('brands',$brands);
         
        $this->assign('pdf_cates',$this->pdf_cate);
        //发货时间
        $goods_times=Db::name('goods_time')->where('status',2)->order('sort asc')->column('id,name');
        $this->assign('goods_times',$goods_times);
        $this->assign('store_sures',config('store_sures'));
        $this->assign('is_rohs',config('is_rohs'));
    }
    /*删除前检查  */
    public function del_before($ids){
        $m=$this->m;
        
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
        $admin=$this->admin;
        //判断供应商级别
        if(empty($data['shop'])){
            $data['shop']=$admin['shop'];
        }
        $data['shop_type']=Db::name('shop')->where('id',$data['shop'])->value('type');
        //得到分类
        if(empty($data['cid3'])){
            return '分类必须选择';
        }else{
            $data['cid']=$data['cid3'];
            unset($data);
        }
        $table=$this->table;
         
        //阶梯价格
        if(!empty($data['nums'])){ 
            foreach($data['nums'] as $k=>$v){
                $data['price1s'][$k]=round($data['price1s'][$k],5);
                $data['price2s'][$k]=round($data['price2s'][$k],5); 
            }
        }
        return 1;
    }
    /*额外添加的信息，一般是图片文件 */
    public function add_do_after($id,$data){
        
        $table=$this->table;
        $pathid='goods_file/'.$id.'/';
        $data_update=[];
        $path='upload/';
        //没有目录创建目录
        if(!is_dir($path.$pathid)){
            mkdir($path.$pathid);
        }
        //产品图片
        if(!empty($data['pic']) && is_file($path.$data['pic'])){ 
            //获取后缀名,复制文件
            $ext=substr($data['pic'], strrpos($data['pic'],'.'));
            //原图
            $data_update['pic0']=$pathid.date('Ymd-His').'pic0'.$ext;
            copy($path.$data['pic'],$path.$data_update['pic0']);
            //缩略图
            $pic_conf=config('pic_'.$table);
            $data_update['pic']=zz_set_file($data['pic'],$pathid,$pic_conf);
        }else{
            $data_update['pic']='';
            $data_update['pic0']='';
        }
        
        
        $m=$this->m;
        $m->where('id',$id)->update($data_update); 
        //添加阶梯价格
        if(!empty($data['nums'])){
            $data_price=[];
            foreach($data['nums'] as $k=>$v){
                $data_price[]=[
                    'pid'=>$id,
                    'num'=>$v,
                    'price1'=>$data['price1s'][$k], 
                ];
            }
            $m_price=new GoodsPriceModel();
            $m_price->insertAll($data_price);
        }
        //数据文件
        $pdf_cates=$this->pdf_cate;
        $data_file=[];
        foreach($pdf_cates as $k=>$v){
            $file=$data['pdf'.$k.'_url'];
            if(!empty($file) && is_file($path.$file)){
                 
                $file_new=zz_set_file_name($file,$pathid,'pdf'.$k);
                $data_file[]=[
                    'pid'=>$id,
                    'type'=>$k,
                    'name'=>$data['pdf'.$k.'_name'],
                    'url'=>$file_new,
                    'sort'=>intval($data['pdf'.$k.'_sort']),
                ];
            }
        }
        if(!empty($data_file)){
            $m_file=new GoodsFileModel();
            $m_file->insertAll($data_file);
        } 
        return 1;
    }
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        //文件处理
        if(!empty($data['pic'])){
            
            $pathid='goods_file/'.$data['id'].'/';
            $path='upload/';
            $data_update=[];
            //没有目录创建目录
            if(!is_dir($path.$pathid)){
                mkdir($path.$pathid);
            }
            if(!empty($data['pic']) && is_file($path.$data['pic'])){
                //有更改
                if(strpos($data['pic'], $pathid)!==0){
                    //获取后缀名,复制文件
                    $ext=substr($data['pic'], strrpos($data['pic'],'.'));
                    //原图
                    $data['pic0']=$pathid.date('Ymd-His').'pic0'.$ext;
                    
                    copy($path.$data['pic'],$path.$data['pic0']);
                    //缩略图
                    $pic_conf=config('pic_goods');
                    $data['pic']=zz_set_file($data['pic'],$pathid,$pic_conf);
                }
            }else{
                $data['pic']='';
                $data['pic0']='';
            }
            
        }
        
        //添加阶梯价格
        if(!empty($data['nums'])){
            $data_price=[];
            foreach($data['nums'] as $k=>$v){
                $data_price[]=[
                    'pid'=>$data['id'],
                    'num'=>$v,
                    'price1'=>$data['price1s'][$k], 
                ];
            } 
            //阶梯数量
            $m_price=new GoodsPriceModel();
            $prices=$m_price->get_all($data['id']);
            if(count($prices) != count($data_price)){
                $content['prices']=$data_price;
            }else{
                $i=0;
                foreach($prices as $k=>$v){
                    if($v['num'] != $data_price[$i]['num'] || $v['price1'] != $data_price[$i]['price1'] ){
                        $content['prices']=$data_price;
                        break;
                    }
                    $i++;
                }
            }
            
        }
        //文件处理
        $m_file=new GoodsFileModel(); 
        $pdfs=$m_file->get_all_by_id($data['id']);
        $pdf_cates=$this->pdf_cate;
        $path='upload/';
        $table=$this->table;
        $pathid='goods_file/'.$data['id'].'/';
        $data_file=[];
        //有新增修改，没有删除
        foreach($pdf_cates as  $k=>$v){
            $file=$data['pdf'.$k.'_url'];
            if(!empty($file) && is_file($path.$file)){
                $file_new=zz_set_file_name($file,$pathid,'pdf'.$k);
                if(isset($pdfs[$k])){
                    if($pdfs[$k]['name'] != $data['pdf'.$k.'_name']){
                        $data_file['edit'][$k]['name']=$data['pdf'.$k.'_name'];
                    }
                    if($pdfs[$k]['url'] != $file_new){
                        $data_file['edit'][$k]['url']=$file_new;
                    }
                    if($pdfs[$k]['sort'] != $data['pdf'.$k.'_sort']){
                        $data_file['edit'][$k]['sort']=$data['pdf'.$k.'_sort'];
                    }
                }else{
                    $data_file['add'][$k]=[
                        'pid'=>$data['id'],
                        'type'=>$k,
                        'name'=>$data['pdf'.$k.'_name'],
                        'url'=>$file_new,
                        'sort'=>intval($data['pdf'.$k.'_sort']),
                    ];
                }
                
            }
        }
        if(!empty($data_file)){
            $content['files']=$data_file;
        }
        
        return 1;
    }
    /* 查看详情 */
    public function edit_after($info){
      
        $m_cate=new GoodsCateModel();
        $cate_info=$m_cate->cate_info($info['cid']);
      
        if(empty($cate_info)){
            $cid1=0;
            $cid2=0;
            $cid3=0;
        } else{
            $cid1=$cate_info['cid1'];
            $cid2=$cate_info['cid2'];
            $cid3=$cate_info['cid3'];
        }
        
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2); 
        $this->assign('cid3',$cid3); 
        //阶梯数量
        $m_price=new GoodsPriceModel();
        $prices=$m_price->get_all($info['id']);
        $this->assign('prices',$prices); 
        //文件
        $m_file=new GoodsFileModel();
        $pdfs=$m_file->get_all_by_id($info['id']);
        $this->assign('pdfs',$pdfs); 
    }
    /* 审核后的操作 */
    public function review_after($info,$status){
        //更新分类产品数量
        $m_cate= new GoodsCateModel();
        $m_cate->set_number_by_cate3($info['cid']);
        return 1;
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
        
        $m=$this->m;
        $m_cate=new GoodsCateModel();
        $cate_info=$m_cate->cate_info($info['cid']);
        if(empty($cate_info)){
            $cate_info['cid1']=0;
            $cate_info['cid2']=0;
        } 
        $this->assign('cid1',$cate_info['cid1']);
        $this->assign('cid2',$cate_info['cid2']);
        if(isset($change['cid'])){
            $cate_info=$m_cate->cate_info($change['cid']);
            if(empty($cate_info)){
                $fname='分类不存在了';
            }else{
                $fname=$cate_info['cname1'].'-'.$cate_info['cname2'].'-'.$cate_info['cname3']; 
            } 
            $this->assign('fname',$fname);
        }
        //阶梯数量
        $m_price=new GoodsPriceModel();
        $prices=$m_price->get_all($info['id']);
        $this->assign('prices',$prices); 
        //文件
        $m_file=new GoodsFileModel();
        $pdfs=$m_file->get_all_by_id($info['id']);
        $this->assign('pdfs',$pdfs); 
    }
    /* 编辑审核前的操作 */
    public function edit_review_before($info1){
        //得到修改的字段
        $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
        $change=json_decode($change,true);
        if(isset($change['prices'])){
           //先删除再添加
           $m_price=new GoodsPriceModel();
           $m_price->where('pid',$info1['pid'])->delete();
           $m_price->insertAll($change['prices']);
           unset($change['prices']);
        }
        //分类变化
        if(isset($change['cid'])){
            $m=$this->m;
            $cid=$m->where('id',$info1['pid'])->value('cid');
            $m->where('id',$info1['pid'])->update(['cid'=>$change['cid']]);
            //分类更新后开始计算分类所属产品的数量
            $m_cate=new GoodsCateModel();
            $m_cate->set_number_by_cate3($cid);
            $m_cate->set_number_by_cate3($change['cid']);
        }
        //文件变化
        if(isset($change['files'])){
            $m_file=new GoodsFileModel();
            $pdfs=$m_file->get_all_by_id($info1['pid']);
            if(isset($change['files']['edit'])){
                foreach($change['files']['edit'] as $k=>$v){
                    $m_file->where(['pid'=>$info1['pid'],'type'=>$k])->update($v);
                }
            }
            //新增的要检查是否已存在，存在则更新
            if(isset($change['files']['add'])){
                foreach($change['files']['add'] as $k=>$v){
                    if(isset($pdfs[$k])){
                        $m_file->where('id',$pdfs[$k]['id'])->update($v);
                    }else{
                        $m_file->insert($v);
                    } 
                }
            }
            unset($change['files']);
        }
        return $change;
    }
    /**
     * 产品规格
     * @adminMenu(
     *     'name'   => '产品规格',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规格',
     *     'param'  => ''
     * )
     */
    public function param_edit()
    {
        $m=$this->m;
        $id=$this->request->param('id',0,'intval');
        $info=$m
        ->alias('p') 
        ->where('p.id',$id)
        ->find();
        if(empty($info)){
            $this->error('数据不存在');
        }
        $this->assign('info',$info);
        if($this->isshop){
            $admin=$this->admin;
            if($admin['shop']!=1 && $admin['shop']!=$info['shop']){
                $this->error('只能查看自己店铺的编辑信息');
            }
            $this->where_shop=$info['shop'];
        }
        //要先获取模板
        $m_cate=new GoodsCateModel();
        $cate_info=$m_cate->cate_info($info['cid']);
        $where_template=[
            'status'=>2
        ];
        if(empty($cate_info)){
            $where_template['cid']=$info['cid'];
        }else{
            $where_template['cid']=['in',[$info['cid'],$cate_info['cid1'],$cate_info['cid2']]];
        }
        $templates=Db::name('goods_template')->where($where_template)->order('sort asc')->column('id,name');
        $this->assign('templates', $templates);
         
        $params=Db::name('goods_param_goods')
        ->alias('pg')
        ->join('cmf_goods_param p','p.id=pg.param')
        ->join('cmf_lan l','l.id=pg.lid','left')
        ->where('pg.goods',$id)
        ->column('pg.*,p.name as param_name,l.name as lan_name');
        $this->assign('params', $params);
        //多语言
        $lans=session('admin_lans'); 
        $lans_json=[];
        foreach($lans as $k=>$v){
            $lans_json[]=[
                'id'=>$k,
                'name'=>$v,
            ];
        }
        $lans_json=json_encode($lans_json);
        $this->assign('lans', $lans);
        $this->assign('lans_json', $lans_json);
        
        return $this->fetch();
    }
    /**
     * 产品规格编辑详情
     * @adminMenu(
     *     'name'   => '产品规格编辑详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规格编辑详情',
     *     'param'  => ''
     * )
     */
    public function param_edit_info()
    {
        $m=$this->m;
        $id=$this->request->param('id',0,'intval');
        $table=$this->table;
        //获取编辑信息
        $m_edit=Db::name('edit');
        $info1=$m_edit
        ->alias('p')
        ->field('p.*,a.user_nickname as aname,r.user_nickname as rname')
        ->join('cmf_user a','a.id=p.aid','left')
        ->join('cmf_user r','r.id=p.rid','left')
        ->where('p.id',$id)
        ->find();
        
        if(empty($info1)){
            $this->error('编辑信息不存在');
        }
        $admin=$this->admin;
        if($admin['shop']!=1 && $admin['shop']!=$info1['shop']){
            $this->error('只能查看自己店铺的编辑信息');
        }
        //获取原信息
        $info=$m
        ->alias('p')
        ->field('p.*,a.user_nickname as aname,r.user_nickname as rname')
        ->join('cmf_user a','a.id=p.aid','left')
        ->join('cmf_user r','r.id=p.rid','left')
        ->where('p.id',$info1['pid'])
        ->find();
        if(empty($info)){
            $this->error('编辑关联的信息不存在');
        }
        //获取改变的信息
        $change=Db::name('edit_info')->where('eid',$id)->value('content');
        $change=json_decode($change,true);
        
        
        $this->assign('info',$info);
        $this->assign('info1',$info1);
        $this->assign('change',$change);
       
        
         
        //要先获取模板
        $m_cate=new GoodsCateModel();
        $cate_info=$m_cate->cate_info($info['cid']);
        $where_template=[
            'status'=>2
        ];
        if(empty($cate_info)){
            $where_template['cid']=$info['cid'];
        }else{
            $where_template['cid']=['in',[$info['cid'],$cate_info['cid1'],$cate_info['cid2']]];
        }
        $templates=Db::name('goods_template')->where($where_template)->order('sort asc')->column('id,name');
        $this->assign('templates', $templates);
        //改变的模板
        if(isset($change['template'])){
            $template_name=Db::name('goods_template')->where('id',$change['template'])->value('name');
            $this->assign('template_name', $template_name);
        }
        $params=Db::name('goods_param_goods')
        ->alias('pg')
        ->join('cmf_goods_param p','p.id=pg.param')
        ->join('cmf_lan l','l.id=pg.lid','left')
        ->where('pg.goods',$info['id'])
        ->column('pg.*,p.name as param_name,l.name as lan_name');
        $this->assign('params', $params);
        //新参数
        if(isset($change['add'])){
            $lan_ids=[];
            $param_ids=[];
            foreach($change['add'] as $k=>$v){
                $lan_ids[]=$v['lid'];
                $param_ids[]=$v['param'];
            }
            $lan_names=Db::name('lan')->where('id','in',$lan_ids)->column('id,name');
            $param_names=Db::name('goods_param')->where('id','in',$param_ids)->column('id,name');
            $this->assign('lan_names', $lan_names);
            $this->assign('param_names', $param_names);
        }
        
        return $this->fetch();
    }
    /**
     * 产品规格编辑提交
     * @adminMenu(
     *     'name'   => '产品规格编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规格编辑提交',
     *     'param'  => ''
     * )
     */
    public function param_edit_do()
    {
        $m=$this->m;
        $table=$this->table;
        $flag='产品规格';
        $data=$this->request->param(); 
        
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
            'url'=>url('param_edit_info','',false,false),
            'rstatus'=>1,
            'rid'=>0,
            'rtime'=>0,
            'shop'=>$admin['shop'],
        ];
        $update['adsc']=(empty($data['adsc']))?('修改了'.$flag.'信息'):$data['adsc'];
        
        $content=[];
        //先判断模板 
        if(empty($info['template']) || $info['template']!=$data['template']){
            $content['template']=$data['template'];
        }
        $data_param=[];
      
        //单语言
        if(!empty($data['param0'])){
            foreach($data['param0'] as $k=>$v){
                $data_param[$k.'-0']=[
                    'goods'=>$info['id'],
                    'param'=>$k,
                    'val'=>$v,
                    'lid'=>0,
                    'sort'=>$data['sorts'][$k],
                ];
            }
        }
        //多语言
        if(!empty($data['param00'])){
            foreach($data['param00'] as $k=>$v){
                //多语言遍历
                foreach($data['param'.$k] as $kk=>$vv){
                    $data_param[$k.'-'.$kk]=[
                        'goods'=>$info['id'],
                        'param'=>$k,
                        'val'=>$vv,
                        'lid'=>$kk,
                        'sort'=>$data['sorts'][$k],
                    ];
                } 
            }
        }
        
        //有模板更改就是全部改变，没有就是一个个比较
        if(isset($content['template'])){
            $content['add']=$data_param;
        }else{
            //部分更改要先获取原参数
            $params=Db::name('goods_param_goods')->where('goods',$info['id'])->order('sort asc')->column('*');
            $params0=[];
            foreach($params as $k=>$v){
                $params0[$v['param'].'-'.$v['lid']]=[ 
                    'id'=>$k,
                    'val'=>$v['val'],  
                ];
            }
            foreach($params0 as $k=>$v){
              
                if(isset($data_param[$k])){
                    if($v['val']!=$data_param[$k]['val']){
                        $content['edit'][$v['id']]=$data_param[$k]['val'];
                    }
                }else{
                    //有不同，直接覆盖
                    $content['add']=$data_param;
                    break;
                }
            }
            foreach($data_param as $k=>$v){
                if(empty($params0[$k])){ 
                    //有不同，直接覆盖
                    $content['add']=$data_param;
                    break;
                }
            }
            if(isset($content['add']) && isset($content['edit'])){
                unset($content['edit']);
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
            'link'=>url('param_edit_info',['id'=>$eid]),
            'shop'=>$admin['shop'],
        ];
        
        zz_action($data_action, $admin);
        
        $m_edit->commit();
        //判断是否直接审核
        $rule='param_edit_review';
        $res=$this->check_review($admin,$rule);
        if($res){
            $this->redirect($rule,['id'=>$eid,'rstatus'=>2,'rdsc'=>'直接审核']);
        }
        
        $this->success('已提交修改');
    }
    /**
     * 产品规格编辑审核
     * @adminMenu(
     *     'name'   => '产品规格编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '产品规格编辑审核',
     *     'param'  => ''
     * )
     */
    public function param_edit_review()
    {
        //审核编辑的信息
        $status=$this->request->param('rstatus',0,'intval');
        $id=$this->request->param('id',0,'intval');
        if(($status!=2 && $status!=3) || $id<=0){
            $this->error('信息错误');
        }
        $m=$this->m;
        $table=$this->table;
        $flag='产品规格';
        $m_edit=Db::name('edit');
        $info1=$m_edit
        ->field('e.*,a.user_nickname as aname')
        ->alias('e')
        ->join('cmf_user a','a.id=e.aid')
        ->where('e.id',$id)
        ->find();
        if(empty($info1)){
            $this->error('无效编辑信息');
        }
        $info=$m->where('id',$info1['pid'])->find();
        if(empty($info)){
            $this->error('编辑关联的信息不存在');
        }
        if($info1['rstatus']!=1){
            $this->error('编辑信息已被审核！不能重复审核');
        }
        
        $admin=$this->admin;
        //其他店铺的审核判断
        if($admin['shop']!=1 && $info1['shop']!=$admin['shop']){
            $this->error('不能审核其他店铺的信息');
        }
        
        $time=time();
        
        $m->startTrans();
        
        $update=[
            'rid'=>$admin['id'],
            'rtime'=>$time,
            'rstatus'=>$status,
        ];
        $review_status=$this->review_status;
        $update['rdsc']=$this->request->param('rdsc','');
        if(empty($update['rdsc'])){
            $update['rdsc']=$review_status[$status];
        }
        
        //只有未审核的才能更新
        $where=[
            'id'=>$id,
            'rstatus'=>1,
        ];
        $row=$m_edit->where($where)->update($update);
        if($row!==1){
            $m->rollback();
            $this->error('审核失败，请刷新后重试');
        }
        //是否更新,2同意，3不同意
        if($status==2){
            
            //组装更新数据
            $update_info=[
                'time'=>$time,
            ];
            //得到修改的字段
            $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
            $change=json_decode($change,true);
            $m_pg=Db::name('goods_param_goods');
            //如果有添加，就先全部删除，后添加
            if(isset($change['add'])){
                 $m_pg->where('goods',$info1['pid'])->delete();
                 $m_pg->insertAll($change['add']);
                 unset($change['add']);
            }elseif(isset($change['edit'])){
                 foreach($change['edit'] as $k=>$v){
                     $m_pg->where('id',$k)->update(['val'=>$v]);
                 }
                 unset($change['edit']);
            }
            foreach($change as $k=>$v){
                $update_info[$k]=$v;
            }
            
            $row=$m->where('id',$info1['pid'])->update($update_info);
            if($row!==1){
                $m->rollback();
                $this->error('信息更新失败，请刷新后重试');
            }
            
        }
        
        //审核成功，记录操作记录,发送审核信息
        $data_action=[
            'aid'=>$admin['id'],
            'time'=>$time,
            'ip'=>get_client_ip(),
            'action'=>$admin['user_nickname'].'审核'.$info1['aid'].'-'.$info1['aname'].'对'.$flag.$info['id'].'-'.$info['name'].'的编辑为'.$review_status[$status],
            'table'=>$table,
            'type'=>'edit_review',
            'pid'=>$info1['pid'],
            'link'=>url('param_edit_info',['id'=>$info1['id']]),
            'shop'=>$admin['shop'],
        ];
        
        zz_action($data_action,['aid'=>$info1['aid']]);
        
        $m->commit();
        $this->success('审核成功');
    }
    
    /**
     * 产品导入
     * @adminMenu(
     *     'name'   => ' 产品导入',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 100,
     *     'icon'   => '',
     *     'remark' => ' 产品导入',
     *     'param'  => ''
     * )
     */
    public function import()
    {
        dump($_POST);
        dump($_FILES);
        $excel_url=$this->request->param('excel_url','');
        $path='upload/';
        $file =$path.$excel_url;
        if(empty($excel_url) || !is_file($file)){
            $this->error('没有上传excel文件');
        }
        
        // 读取excel文件
        try {
            $inputFileType = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($file);
        } catch(\Exception $e) {
            die('加载文件发生错误：'.pathinfo($file,PATHINFO_BASENAME).':'.$e->getMessage());
        }
                
        // 确定要读取的sheet，什么是sheet，看excel的右下角，真的不懂去百度吧
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
      
        //从第二行开始读取数据
        $data_goods=[]; 
        $data_file=[]; 
        $data_val=[];
       /*  [0] => array(16) {
            [0] => string(11) "供应商id"
            [1] => string(8) "分类id"
            [2] => string(12) "库存编号"
                [3] => string(12) "完整型号"
                    [4] => string(8) "品牌id"
                        [5] => string(12) "封装规格"
            [6] => string(13) " 参数描述"
                [7] => string(6) "库存"
                    [8] => string(6) "单价"
                        [9] => string(27) "电脑存档对应pdf编号"
            [10] => string(6) "整包"
                [11] => string(9) "起订量"
                    [12] => string(6) "倍数"
            [13] => string(12) "大陆交货"
                [14] => string(12) "香港交货"
                    [15] => string(12) "库存类型" */
        
        $file_path='file_upload/';
        $store_codes=[];
        $admin=$this->admin;
        $time=time();
        $rows= $sheet->rangeToArray('A2:P'.$highestRow, NULL, TRUE, FALSE);
        
        foreach($rows as $k=>$rowData){
             
            $store_code=trim($rowData[2]);
            $store_codes[]=$store_code;
            $data_goods[$store_code]=[
                'shop'=>intval($rowData[0]),
                'cid'=>intval($rowData[1]),
                'store_code'=>$store_code,
                'code'=>trim($rowData[3]),
                'name'=>trim($rowData[3]),
                'dsc'=>trim($rowData[3]),
                'brand'=>intval($rowData[4]),
                'box'=>trim($rowData[5]),
                'store_num'=>intval($rowData[7]),
                'price1'=>round($rowData[8],5),
                'num_one'=>intval($rowData[10]),
                'num_min'=>intval($rowData[11]),
                'num_times'=>intval($rowData[12]),
                'goods_time1'=>intval($rowData[13]),
                'goods_time2'=>intval($rowData[14]),
                'store_sure'=>intval($rowData[15]), 
                'status'=>2,
                'aid'=>$admin['id'],
                'atime'=>$time,
                'rid'=>$admin['id'],
                'rtime'=>$time, 
                'time'=>$time,
            ];
            $data_val[$store_code]=[
                'name'=>$data_goods[$store_code]['name'],
                'dsc'=>trim($rowData[6]), 
                'lid'=>1,
            ];
            $url=trim($rowData[9]);
            if(!empty($url)){
                $data_file[$store_code]=[
                    'name'=>$url,
                    'url'=>$file_path.$url,
                    'type'=>1,
                ];
            }
           
           
        }
        if(empty($data_goods)){
            $this->error('没有数据',url('index'));
        }
       $m_goods=$this->m;
       $m_file=new GoodsFileModel();
       $m_val=Db::name('goods_val');
       $m_goods->startTrans();
       //添加产品
       $m_goods->insertAll($data_goods);
       $pids=$m_goods->where('store_code','in',$store_codes)->column('store_code,id','store_code');
       if(!empty($data_file)){
           foreach($data_file as $k=>$v){
               $data_file[$k]['pid']=$pids[$k];
           }
           $m_file->insertAll($data_file);
       }
       
       foreach($data_val as $k=>$v){
           $data_val[$k]['pid']=$pids[$k];
       }
       $m_val->insertAll($data_val);
       $m_goods->commit();
       $this->success('导入成功',url('index'));
        exit('dd');
    }
    
}

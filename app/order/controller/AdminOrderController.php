<?php
 
namespace app\order\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
use app\goods\model\GoodsModel; 
use app\goods\model\GoodsPriceModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsFileModel;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Border;
use app\order\model\OrderModel;
/* 订单的添加 */
class AdminOrderController extends AdminInfoController
{
   
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='订单';
        $this->table='order';
        $this->m=new OrderModel();
        
        $this->isshop=0;
        $this->islan=0;
        
        $this->assign('flag','订单');
         
    }
    //测试列表
    public function index1()
    {
        $table=$this->table;
        $m=$this->m;
        $admin=$this->admin;
        
        $data=$this->request->param();
        $where=[];
        
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        if(empty($data['order_type'])){
            $data['order_type']=0;
        }else{
            $where['type']=['eq',$data['order_type']];
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
                $where_goods['cid']=['in',$cids];
            }else{
                $cid2=$data['cid2'];
                if(empty($data['cid3'])){
                    $cids=$m_cate->get_cids_by_fid($cid2);
                    $where_goods['cid']=['in',$cids];
                }else{
                    $cid3=$data['cid3'];
                    $where_goods['cid']=['eq',$cid3];
                }
            }
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2);
        $this->assign('cid3',$cid3);
        
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
        
        $res=$m->get_page($data,$where);
        
        $this->assign('list',$res['list']);
        $this->assign('page',$res['page']);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
        
        
        return $this->fetch(); 
    }
    /**
     * 订单列表
     * @adminMenu(
     *     'name'   => '订单列表',
     *     'parent' => 'order/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单列表',
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
        
        //状态
        if(empty($data['status'])){
            $data['status']=0;
        }else{
            $where['status']=['eq',$data['status']];
        }
        if(empty($data['order_type'])){
            $data['order_type']=0;
        }else{
            $where['type']=['eq',$data['order_type']];
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
                $where_goods['cid']=['in',$cids];
            }else{
                $cid2=$data['cid2'];
                if(empty($data['cid3'])){
                    $cids=$m_cate->get_cids_by_fid($cid2);
                    $where_goods['cid']=['in',$cids];
                }else{
                    $cid3=$data['cid3'];
                    $where_goods['cid']=['eq',$cid3];
                } 
            } 
        }
        $this->assign('cid1',$cid1);
        $this->assign('cid2',$cid2); 
        $this->assign('cid3',$cid3); 
        
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
      
        $res=$m->get_page($data,$where);
        
        $this->assign('list',$res['list']);
        $this->assign('page',$res['page']);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
         
      
        return $this->fetch();
    }
     
    
    /**
     * 订单详情
     * @adminMenu(
     *     'name'   => '订单详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id=$this->request->param('id',0,'intval'); 
        $uid=session('user.id');
        $m_order=new OrderModel();
        $info=$m_order->where('id',$id)->find();
       
        $info=$info->getData();
      
        $goods_list=Db::name('order_goods')->where('oid','eq',$id)->column('*','goods');
        $ids=array_keys($goods_list);
        $m_goods=new GoodsModel();
        $goods_list0=$m_goods->goods_list(1,1,$ids);
        
        $this->assign('info',$info);
        $this->assign('goods_list',$goods_list0);
        $this->cates();
        return $this->fetch();
    }
    /**
     * 订单支付审核
     * @adminMenu(
     *     'name'   => '订单支付审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 订单支付批量同意
     * @adminMenu(
     *     'name'   => '订单支付批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    
    
    /**
     * 订单编辑提交
     * @adminMenu(
     *     'name'   => '订单编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 订单编辑列表
     * @adminMenu(
     *     'name'   => '订单编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 订单审核详情
     * @adminMenu(
     *     'name'   => '订单审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 订单信息编辑审核
     * @adminMenu(
     *     'name'   => '订单编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
     
    
    public function cates($type=3){
        parent::cates($type);
        
         
        $m_brand=new GoodsBrandModel();
        $brands=$m_brand->get_all();
        $this->assign('brands',$brands);
         
        //发货时间
        $goods_times=Db::name('goods_time')->where('status',2)->order('sort asc')->column('id,name');
        $this->assign('goods_times',$goods_times);
        $this->assign('store_sures',config('store_sures'));
        $this->assign('is_rohs',config('is_rohs'));
        //收款账号
        $where=['status'=>2,'shop'=>1];
        $paytypes=Db::name('paytype')->where($where)->column('id,name');
        $this->assign('paytypes',$paytypes);
        //付款方式
        $pay_type1s=config('pay_type1');
        //在线付款方式
        $pay_type2s=config('pay_type2');
        //付款金额比例
        $pay_type3s=config('pay_type3');
        $send_statuss=config('send_status');
        $pay_statuss=config('pay_status');
        $order_statuss=config('order_status'); 
        $order_types=config('order_type');
        $this->assign('pay_type1s',$pay_type1s);
        $this->assign('pay_type2s',$pay_type2s);
        $this->assign('pay_type3s',$pay_type3s);
        $this->assign('send_statuss',$send_statuss);
        $this->assign('pay_statuss',$pay_statuss);
        $this->assign('order_statuss',$order_statuss);
        $this->assign('order_types',$order_types);
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
        //订单图片
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
        //更新分类订单数量
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
            //分类更新后开始计算分类所属订单的数量
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
     * 订单导入
     * @adminMenu(
     *     'name'   => ' 订单导入',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 100,
     *     'icon'   => '',
     *     'remark' => ' 订单导入',
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
       //添加订单
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

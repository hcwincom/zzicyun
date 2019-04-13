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
/* 订单支付的添加 */
class AdminOrderPayController extends AdminInfoController
{
   
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='订单支付';
        $this->table='order_pay';
        $this->m=Db::name('order_pay');
        
        $this->isshop=0;
        $this->islan=0;
        
        $this->assign('flag','订单支付');
         
    }
   
    /**
     * 订单支付列表
     * @adminMenu(
     *     'name'   => '订单支付列表',
     *     'parent' => 'order/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付列表',
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
            $where['oid_type']=['eq',$data['order_type']];
        }
        if(empty($data['pay_type'])){
            $data['pay_type']=0;
        }else{
            $where['pay_type']=['eq',$data['pay_type']];
        }
        if(empty($data['paytype'])){
            $data['paytype']=0;
        }else{
            $where['paytype']=['eq',$data['paytype']];
        }
        
        if(empty($data['typetype'])){
            $data['typetype']=0;
        }else{
            $where['type']=['eq',$data['typetype']];
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
        $times=
        array (
            1 =>
            array (
                0 => 'utime',
                1 => '支付时间',
            ),
            2 =>
            array (
                0 => 'rtime',
                1 => '审核时间',
            ), 
        );
        zz_search_time($times,$data,$where);
        
        $list=$m->where($where)->order('id desc')->paginate();
        $page=$list->appends($data)->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        
        $this->assign('data',$data);
        $this->assign('types',$types);
        $this->assign('times',$times);
        $this->assign("search_types", $search_types);
        
        $this->cates(1);
         
      
        return $this->fetch();
    }
     
    
    /**
     * 订单支付详情
     * @adminMenu(
     *     'name'   => '订单支付详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        
        $lan1=1;
        $lan2=1;
        $id=$this->request->param('id',0,'intval');
       
        $m_order=new OrderModel();
        $info=$m_order->where('id',$id)->find();
        $info=$info->getData();
        $uid=$info['uid'];
        
        $goods_list=Db::name('order_goods')->where('oid','eq',$id)->column('*','goods');
        $ids=array_keys($goods_list);
        $m_goods=new GoodsModel();
        $goods_list0=$m_goods->goods_list($lan1,$lan2,$ids);
        $m_brand=new GoodsBrandModel();
        $brands=$m_brand->get_list($lan1,$lan2);
        //循环添加品牌名和库存
        foreach($goods_list as $k=>$v){
            
            if(isset($brands[$v['brand']])){
                $goods_list[$k]['brand_name']=$brands[$v['brand']]['name'];
            }else{
                $goods_list[$k]['brand_name']='';
            }
            if(isset($goods_list0[$k])){
                $goods_list[$k]['store_num']=$goods_list0[$k]['store_num'];
            }else{
                $goods_list[$k]['store_num']=0;
            } 
        }
        //状态操作记录
        $status_dos=Db::name('order_status')->where('oid',$info['id'])->column('');
        //获取用户名和管理员操作人
         $uids=[];
         $unames=[];
         if($info['uid']>0){
             $uids[]=$info['uid'];
         }
         if($info['aid']>0){
             $uids[]=$info['aid'];
         }
         if($info['rid']>0){
             $uids[]=$info['rid'];
         }
         foreach($status_dos as $k=>$v){
             $uids[]=$v['aid'];
         }
         
        $unames=Db::name('user')->where('id','in',$uids)->column('id,user_nickname as name');
        $this->assign('info',$info);
        $this->assign('goods_list',$goods_list);
        $this->assign('unames',$unames);
        $this->assign('status_dos',$status_dos);
        $this->cates();
        return $this->fetch();
    }
    
    /**
     * 订单支付支付批量同意
     * @adminMenu(
     *     'name'   => '订单支付支付批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付支付批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    
    
    /**
     * 订单支付编辑提交
     * @adminMenu(
     *     'name'   => '订单支付编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 订单支付编辑列表
     * @adminMenu(
     *     'name'   => '订单支付编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 订单支付审核详情
     * @adminMenu(
     *     'name'   => '订单支付审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 订单支付信息编辑审核
     * @adminMenu(
     *     'name'   => '订单支付编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '订单支付编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
     
    
    public function cates($type=3){
        parent::cates($type);
         
       
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
       
        $pay_statuss=config('pay_status');
        $order_statuss=config('order_status'); 
        $order_types=config('order_type');
        $this->assign('pay_type1s',$pay_type1s);
        $this->assign('pay_type2s',$pay_type2s);
        $this->assign('pay_type3s',$pay_type3s);
        
        $this->assign('pay_statuss',$pay_statuss);
        $this->assign('order_statuss',$order_statuss);
        $this->assign('order_types',$order_types);
    }
    
    /* 编辑中的处理 */
    public function edit_do_before(&$content,&$data){
        
        
        return 1;
    }
    /* 查看详情 */
    public function edit_after($info){
      
        
    }
    /* 审核后的操作 */
    public function review_after($info,$status){
       
        return 1;
    }
    /* 编辑详情 */
    public function edit_info_after($info,$change){
         
    }
    /* 编辑审核前的操作 */
    public function edit_review_before($info1){
        //得到修改的字段
        $change=Db::name('edit_info')->where('eid',$info1['id'])->value('content');
        $change=json_decode($change,true);
         
        return $change;
    }
   
    /**
     * 订单支付导入
     * @adminMenu(
     *     'name'   => ' 订单支付导入',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 100,
     *     'icon'   => '',
     *     'remark' => ' 订单支付导入',
     *     'param'  => ''
     * )
     */
    public function import()
    {
        
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
       //添加订单支付
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
    /**
     * 确认付款
     * @adminMenu(
     *     'name'   => '确认付款',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '确认付款',
     *     'param'  => ''
     * )
     */
    public function pay_do21()
    {
        $data=$this->request->param(); 
        $flag='确认付款';
        $data=$this->request->param();
        $this->pay_do($data,21,$flag);
    }
    /* 改变订单支付支付状态 */
    public function pay_do($data,$pay_status,$flag){
         
        $m=$this->m;
        $m_pay=Db::name('order_pay');
        $table=$this->table;
        $admin=$this->admin;
        $time=time();
        $oid=intval($data['id']);
        $where_order=['id'=>$oid];
        $update_order=['time'=>$time];
        $where_pay=['oid'=>$oid];
        $update_pay=['rtime'=>$time,'rid'=>$admin['id']];
        switch ($pay_status){
            case 21:
                //付款确认
                $where_order['pay_status']=2;
                $where_pay['type']=1;
                $update_pay['status']=2;
               
                break;
            case 22:
                //付款驳回
                $where_order['pay_status']=2;
                $where_pay['type']=1;
                $update_pay['status']=3;
                break;
            case 41:
                //尾款确认
                $where_order['pay_status']=4;
                $where_pay['type']=2;
                $update_pay['status']=2;
                break;
            case 42:
                //尾款驳回
                $where_order['pay_status']=4;
                $where_pay['type']=2;
                $update_pay['status']=3;
                break;
        }
        $order=$m->where($where_order)->find();
        if(empty($order)){
            $this->error('订单支付信息错误');
        }
        $pay=$m_pay->where($where_pay)->order('id desc')->find();
        if(empty($order)){
            $this->error('订单支付信息错误');
        }
        $update_order['pay_status']=$pay['pay_status_new'];
        //付尾款的为已备货待发货
        if($pay['type']==2){
            $update_order['status']=20;
        }else{
            $update_order['status']=10;
        }
        $m->startTrans();
        $m->where('id',$order['id'])->update($update_order);
        $m_pay->where('id',$pay['id'])->update($update_pay);
        //记录状态更新
        $data_status=[];
        $data_status['pay']=[
            'oid'=>$order['id'],
            'aid'=>$admin['id'],
            'type'=>1,
            'status_old'=>$pay['pay_status_old'],
            'status_new'=>$pay['pay_status_new'],
        ];
         $m->commit();
        $this->success('已提交修改');
    }
}

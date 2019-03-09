<?php
 
namespace app\goods\controller;
 
use app\common\controller\DeskBaseController;
use app\goods\model\GoodsCateModel;
use think\Db; 
  
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandCateModel;
use app\goods\model\GoodsParamModel;
use app\goods\model\GoodsTimeModel;
use app\goods\model\GoodsPriceModel;
use app\goods\model\GoodsFileModel;
class GoodsController extends DeskBaseController
{

    
    /**
     * 产品详情
      */
    public function detail()
    {
         $lan1=$this->lan1;
         $lan2=$this->lan2;
         $id=$this->request->param('id',0,'intval');
           
         //海量产品
         $m_goods=new GoodsModel();
         $goods=$m_goods->get_one($id,$lan1,$lan2);
         $this->assign('info',$goods);
         $this->assign('pdf_cates',[1=>'数据手册',2=>'AD封装库文件',3=>'PADS封装库文件']);
         $this->assign('html','goods_detail');
         //发货时间
         $m_time=new GoodsTimeModel();
         $goods_times=$m_time->get_list($lan1,$lan2);
         $this->assign('goods_times',$goods_times);
          //类似产品
         $this->assign('goods_likes',[]);
         return $this->fetch();
   }
   /**
    * 产品分类
    */
   public function cate_list()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       
       //产品分类
       $m_cate=new GoodsCateModel();
       $list=$m_cate->all_cates($lan1,$lan2);
       $this->assign('list',$list);
       $this->assign('html','cate_list');
      
       return $this->fetch();
   }
   /**
    * 被动选型
    */
   public function goods_search()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       
       //产品分类
       $m_cate=new GoodsCateModel();
       $list=$m_cate->all_cates($lan1,$lan2);
       $this->assign('list',$list);
       //查询
       $data=$this->request->param();
       $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
           ',p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
       $where=['p.status'=>2];
      
       //条件筛选 
       $params=[];
       if(empty($data['cid'])){
           $data['cid']=0;
       }else{
           $cids=$m_cate->get_cids_by_fid($data['cid']);
           if(count($cids)>1){
               $where['p.cid']=['in',$cids];
           }else{
               $where['p.cid']=['eq',$data['cid']];
           }
           //获取模板和参数，用于查询
           $template_ids=Db::name('goods_template')->where('cid','in',$cids)->column('id');
           //查询参数
           if(!empty($template_ids)){
               
               $m_param=new GoodsParamModel();
               $params=$m_param->get_params_by_templates($lan1,$lan2,$template_ids);
           } 
       }
       
      
       //排序 
       $order='p.shop_type asc,';
       if(empty($data['sort'])){
           $data['sort']='';
       }else{
           switch($data['sort']){
               case 'price1':
                   //价格升序
                   $order='p.price1 asc,';
                   break;
               case 'price2':
                   //价格降序
                   $order='p.price1 desc,';
                   break;
               case 'store1':
                   //库存升序
                   $order='p.price1 asc,';
                   break;
               case 'store2':
                   //库存降序
                   $order='p.store2 desc,';
                   break;
           }
       }
       //显示有库存
       if(empty($data['is_store'])){
           $data['is_store']=0;
       }else{
           $where['p.store_num']=['gt',0];
       }
       //rohs
       if(empty($data['is_rohs'])){
           $data['is_rohs']=0;
       }else{
           $where['p.rohs']=['eq',1];
       }
       //品牌
       if(empty($data['brand'])){
           $data['brand']=[];
       }else{
           $where['p.brand']=['in',$data['brand']];
       }
       //参数
       if(empty($data['param'])){
           $data['param']=[];
       }else{
           //参数比较
           $where_param=[ 'lid'=>$lan1];
           $where_param['param']=['in',array_keys($data['param'])];
           $vals=[];
          
           foreach($data['param'] as $k=>$v){
               $param_val=explode(',', $v); 
               array_pop($param_val); 
               $vals=array_merge($vals,$param_val); 
           }
           $where_param['val']=['in',$vals]; 
          
           $ids=Db::name('goods_param_goods')->where($where_param)->column('goods');
          
           if(empty($ids)){
               $where['p.id']=['eq',0];
           }else{
               $where['p.id']=['in',$ids];
           }
       }
       //条件筛选
       if(empty($data['name'])){
           $data['name']='';
       }else{
           $where['p.code|val.name|val.production_code']=['like','%'.$data['name'].'%'];
       }
       $order=$order.'p.sort asc'; 
       $m_goods=new GoodsModel();
       $goods_data=$m_goods->goods_page($lan1,$lan2,$where,$data,$order);
       $list=$goods_data['goods_list'];
       $page=$goods_data['page'];
       $file_list=$goods_data['file_list'];
       $price_list=$goods_data['price_list'];
       
       
       $goods_ids=[];
       $shop_ids=[]; 
       $shop_names=[];
       $goods_list=[];
       
       if(!empty($list)){
           //得到供应商和品牌id
           foreach($list as $k=>$v){
               $shop_ids[]=$v['shop']; 
               $goods_ids[]=$v['id'];
           }
           //供应商名
           $m_shop=new ShopModel();
           $shop_names=$m_shop->get_limit($lan1,$lan2,['p.id'=>['in',$shop_ids]]);
           $type=100;
           foreach($list as $k=>$v){
               if(isset($shop_names[$v['shop']])){
                   $type=$shop_names[$v['shop']]['type'];
               }else{
                   $type=100;
               }
               if(isset($goods_list[$type])){
                   $goods_list[$type]['list'][$v['id']]=$v;
                   $goods_list[$type]['num']++;
               }else{
                   $goods_list[$type]=[
                       'num'=>1,
                       'list'=>[$v['id']=>$v] 
                   ]; 
               }
               
           }
           
       }
       //品牌类型
       $m_brand_cate= new GoodsBrandCateModel();
       $brand_cates=$m_brand_cate->get_all($lan1,$lan2);
       //品牌
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2);
      
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
       
       $this->assign('goods_list',$goods_list);
       $this->assign('shop_names',$shop_names);
       $this->assign('brand_cates',$brand_cates);
       $this->assign('brands',$brands); 
       $this->assign('params',$params); 
       $this->assign('goods_times',$goods_times); 
       $this->assign('price_list',$price_list); 
       $this->assign('file_list',$file_list); 
       $this->assign('data',$data); 
       $this->assign('page',$page); 
       return $this->fetch();
   }
   /**
    * 产品文件下载
    */
   public function file_load()
   {
       $data=$this->request->param();
       $id=intval($data['id']);
       $file=Db::name('goods_file')->where('id',$id)->find();
       
       $path='upload/';
       $file_url=$path.$file['url'];
       $filename=$file['name'];
       
       
       if(is_file($file_url)){
           $fileinfo=pathinfo($file_url);
           $ext=$fileinfo['extension'];
           
           header('Content-type: application/x-'.$ext);
           header('content-disposition:attachment;filename='.$filename);
           header('content-length:'.filesize($file_url));
           readfile($file_url);
           exit;
       }else{
           $this->error('文件损坏，不存在');
       }
   }
   /**
    * 产品文件在线查看
    */
   public function file_read()
   {
       $data=$this->request->param();
       $id=intval($data['id']);
       $file=Db::name('goods_file')->where('id',$id)->find();
       
       $path='upload/';
       $file_url=$path.$file['url'];
       $filename=$file['name'];
       
       
       if(is_file($file_url)){
           $fileinfo=pathinfo($file_url);
           $ext=$fileinfo['extension'];
           
           header('Content-type: application/x-'.$ext);
           header('content-disposition:attachment;filename='.$filename);
           header('content-length:'.filesize($file_url));
           readfile($file_url);
           exit;
       }else{
           $this->error('文件损坏，不存在');
       }
   }
   /**
    * 产品文件
    */
   public function file_list()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $m_file=new GoodsFileModel();
       $data=$this->request->param();
       $where=[];
       if(isset($data['name'])){
           $where['name']=['like','%'.$data['name'].'%']; 
       }else{
           $data['name']=''; 
       }
       
       $list=$m_file->where($where)->paginate(2);
       $page = $list->appends($data)->render();
       $pids=[];
       $goods_list=[];
       if(!empty($list)){
           foreach($list as $k=>$v){
               $pids[$v['pid']]=$v['pid'];
           }
           $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
               ',p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
           $where=['p.id'=>['in',$pids]];
           $goods_list=Db::name('goods')
           ->alias('p')
           ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
           ->where($where) 
           ->column($field);
       }
      
       //品牌
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2);
       $this->assign('list',$list);
       $this->assign('page',$page);
       $this->assign('data',$data); 
       $this->assign('brands',$brands); 
       $this->assign('goods_list',$goods_list); 
       return $this->fetch();
   }

}

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
       $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
           ',p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
       $where=['p.status'=>2];
       $order='p.shop_type asc,p.sort asc';
       //条件筛选 
       $params=[];
       if(!empty($data['cid'])){
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
       $list=Db::name('goods')
       ->alias('p')
       ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1) 
       ->column($field);
       $goods_ids=array_keys($list);
       $shop_ids=[]; 
       $shop_names=[];
       $goods_list=[];
       //阶梯价格
       $price_list=[];
       //产品文件
       $file_list=[];
       if(!empty($list)){
           //得到供应商和品牌id
           foreach($list as $k=>$v){
               $shop_ids[]=$v['shop']; 
              
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
                   $goods_list[$type]['list'][$k]=$v;
                   $goods_list[$type]['num']++;
               }else{
                   $goods_list[$type]=[
                       'num'=>1,
                       'list'=>[$k=>$v] 
                   ]; 
               }
               
           }
           //阶梯价格
           $m_price=new GoodsPriceModel();
           $price_list=$m_price->get_all_by_pids($goods_ids);
           
           //产品文件
           $m_file=new GoodsFileModel();
           $file_list=$m_file->get_all_by_ids($goods_ids);
       }
       //品牌类型
       $m_brand_cate= new GoodsBrandCateModel();
       $brand_cates=$m_brand_cate->get_all($lan1,$lan2);
       //品牌
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2);
       dump($list);
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
       //阶梯价格
       $this->assign('goods_list',$goods_list);
       $this->assign('shop_names',$shop_names);
       $this->assign('brand_cates',$brand_cates);
       $this->assign('brands',$brands); 
       $this->assign('params',$params); 
       $this->assign('goods_times',$goods_times); 
       $this->assign('price_list',$price_list); 
       $this->assign('file_list',$file_list); 
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

}

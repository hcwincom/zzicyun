<?php
 
namespace app\goods\controller;
 
use app\common\controller\DeskBaseController;
use app\goods\model\GoodsCateModel;
use think\Db; 
use app\notice\model\ArticleModel;
use app\notice\model\ArticleCateModel;
use app\goods\model\GoodsFileModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\goods\model\ShopModel;
use app\goods\model\GoodsBrandCateModel;
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
           $where=['is_search'=>1];
           $param_ids
       }
       $list=Db::name('goods')
       ->alias('p')
       ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1) 
       ->column($field);
       
       $shop_ids=[]; 
       $shop_names=[];
       
       if(!empty($list)){
           //得到供应商和品牌id
           foreach($list as $k=>$v){
               $shop_ids[]=$v['shop']; 
           }
           //供应商名
           $m_shop=new ShopModel();
           $shop_names=$m_shop->get_limit($lan1,$lan2,['p.id'=>['in',$shop_ids]]);
            
       }
       //品牌类型
       $m_brand_cate= new GoodsBrandCateModel();
       $brand_cates=$m_brand_cate->get_all($lan1,$lan2);
       //品牌
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2);
       $this->assign('goods_list',$list);
       $this->assign('shop_names',$shop_names);
       $this->assign('brand_cates',$brand_cates);
       $this->assign('brands',$brands); 
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

}

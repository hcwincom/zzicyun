<?php
 
namespace app\shop\controller;
 
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
use app\notice\model\BannerModel;
class ShopController extends DeskBaseController
{

    
    
   /**
    *店铺列表-原厂代理
    */
   public function shop_list2()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $data=$this->request->param();
       $where=['p.status'=>2];
      /*  'shop_types'=>[
           1=>'自营',
           2=>'代售',
           3=>'原厂专营',
           4=>'实力分销商',
           5=>'海外代购',
       ], */
       $types=[2,3];
       $where['p.type']=['in',$types];
       
       
      
       $m_shop=new ShopModel();
       //现获取最新店铺
       $shop_list_new=$m_shop->get_new3($lan1,$lan2,$where);
       //查找条件
       if(!empty($data['type']) && in_array($data['type'],$types)){
           $where['p.type']=$data['type'];
       }else{
           $data['type']=0;
       }
       if(empty($data['char'])){
           $data['char']='#';
       }else{
           $where['val.char']=$data['char'];
       }
       
       $shop_list0=$m_shop->get_limit($lan1,$lan2,$where);
       $shop_list=[];
       foreach($shop_list0 as $k=>$v){
           $shop_list[$v['char']][$k]=$v;
       }
       $this->assign('shop_list',$shop_list);
       //字母
       $this->assign('chars',config('chars'));
       $this->assign('data',$data);
       $this->assign('shop_list_new',$shop_list_new);
       
       //广告图
       $m_banner=new BannerModel();
       $banners=$m_banner->get_banners_by_cname('shop_list2_banner');
       $this->assign('banners',$banners);
      
       $this->assign('html','shop_list2');
       $this->assign('types',$types);
      
       return $this->fetch();
   }
   /**
    *店铺列表-原厂代理
    */
   public function shop_list3()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $data=$this->request->param();
       $where=['p.status'=>2];
        
       $types=[];
       $data['type']=4;
       $where['p.type']=4;
        
       $m_shop=new ShopModel();
       $shop_list_new=$m_shop->get_new3($lan1,$lan2,$where);
       
       if(empty($data['char'])){
           $data['char']='#';
       }else{
           $where['val.char']=$data['char'];
       }
       
       $shop_list0=$m_shop->get_limit($lan1,$lan2,$where);
       $shop_list=[];
       foreach($shop_list0 as $k=>$v){
           $shop_list[$v['char']][$k]=$v;
       }
       $this->assign('shop_list',$shop_list);
       //字母
       $this->assign('chars',config('chars'));
       $this->assign('data',$data);
       $this->assign('shop_list_new',$shop_list_new);
       //广告图
       $m_banner=new BannerModel();
       $banners=$m_banner->get_banners_by_cname('shop_list3_banner');
       $this->assign('banners',$banners);
       
       $this->assign('html','shop_list3');
       $this->assign('types',$types);
       
       return $this->fetch();
   }
   /**
    *品牌制造商
    */
   public function shop_list1()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       $data=$this->request->param();
       $where=['p.status'=>2];
       if(empty($data['char'])){
           $data['char']='';
       }else{
           $where['val.char']=$data['char'];
       }
       //品牌还是品牌供应商，暂定为品牌
       $m_brand=new GoodsBrandModel();
       $brands=$m_brand->get_list($lan1,$lan2,$where);
       $this->assign('brands',$brands);
       //字母
       $this->assign('chars',config('chars'));
       $this->assign('data',$data);
       
       $this->assign('html','shop_list1');
        
       return $this->fetch();
   }
   
   /**
    * 店铺产品列表
    */
   public function shop_goods()
   {
       $lan1=$this->lan1;
       $lan2=$this->lan2;
       
       
       //查询
       $data=$this->request->param();
       //店铺信息
       $m_shop=new ShopModel();
       $shop=$m_shop->get_one($lan1,$lan2,$data['id']);
       if(empty($shop)){
           $this->redirect(url('portal/Index/index'));
       }
       $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
           ',p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
       $where=[
           'p.status'=>2,
           'p.shop'=>$data['id']
       ];
       $order='p.sort asc';
       //条件筛选 
       if(empty($data['name'])){
           $data['name']='';
       }else{
           $where['p.code|val.name|val.production_code']=['like','%'.$data['name'].'%'];
       }
       //排序
       $order='';
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
       $order=$order.'p.sort asc'; 
       $list0=Db::name('goods')
       ->alias('p')
       ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1) 
       ->where($where)
       ->field($field)
       ->paginate(2);
       
       // 获取分页显示
       $page = $list0->appends($data)->render();
       $list=[];
       $goods_ids=[];
       
       //阶梯价格
       $price_list=[];
       //产品文件
       $file_list=[];
       if(!empty($list0)){
           foreach ($list0 as $k=>$v){
               $goods_ids[]=$v['id'];
               $list[$v['id']]=$v;
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
      
       //发货时间
       $m_time=new GoodsTimeModel();
       $goods_times=$m_time->get_list($lan1,$lan2);
       
       $this->assign('goods_lists',$list);
       $this->assign('page',$page);
       $this->assign('info',$shop);
       $this->assign('data',$data);
       //本店铺
       $shop_names=[$shop['id']=>$shop];
       $this->assign('brand_cates',$brand_cates);
       $this->assign('brands',$brands); 
       $this->assign('shop_names',$shop_names); 
       
       $this->assign('goods_times',$goods_times); 
       $this->assign('price_list',$price_list); 
       $this->assign('file_list',$file_list); 
       return $this->fetch();
   }
   

}

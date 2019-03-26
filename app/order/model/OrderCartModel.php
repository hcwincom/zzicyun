<?php
 
namespace app\order\model;

use think\Model;
use think\Db;
use app\goods\model\GoodsModel;
use app\shop\model\ShopModel;
use app\goods\model\GoodsBrandModel;
class OrderCartModel extends Model
{
     
    public function cart_page($lan1,$lan2,$where,$data){
        $nums=$this->field('id,goods,num,type')->where($where)->order('id desc')->paginate(10);
        if(empty($nums)){
            return ['list'=>[],'page'=>null,'brands'=>[],'shops'=>[]];
        }
        // 获取分页显示
        $page = $nums->appends($data)->render();
        
        $goods_ids=[];
        $goods_list=[];
        //阶梯价格
        $price_list=[];
        $list=[];
        if($lan1>1 || $data['type'] >1 ){
           $price_type=2;
       }else{
           $price_type=1;
       }
        foreach($nums as $v){
            $goods_ids[]=$v['goods'];
            $list[$v['id']]=[
                'goods'=>$v['goods'],
                'num'=>$v['num'],  
                'type'=>$v['type']
            ];
        }
        $m_goods=new GoodsModel();
        $res=$m_goods->goods_list($lan1,$lan2,$goods_ids);
        $goods_list=$res['goods_list'];
        $price_list=$res['price_list'];
        $shop_ids=[];
        $brand_ids=[];
        //产品信息，获取价格
        foreach($list as $k=>$v){
            
            if(isset($goods_list[$v['goods']])){
                $v['goods_info']=$goods_list[$v['goods']];
            }else{
                unset($list[$k]);
                continue;
            }
            $v['price']=$v['goods_info']['price'.$price_type];
            if(isset($price_list[$v['goods']])){
                $v['price_list']=$price_list[$v['goods']];
                foreach($price_list[$v['goods']] as $kk=>$vv){
                    if($v['num']>=$vv['num']){
                        $v['price']=$vv['price'.$price_type];
                    }
                }
            }else{ 
                $v['price_list']=[];
            } 
            $list[$k]=$v;
            $shop_ids[$v['goods_info']['shop']]=$v['goods_info']['shop'];
            $brand_ids[$v['goods_info']['brand']]=$v['goods_info']['brand'];
        }
        //供应商名
        $m_shop=new ShopModel();
        $shops=$m_shop->get_limit($lan1,$lan2,['p.id'=>['in',$shop_ids]]);
        
        $m_brand=new GoodsBrandModel();
        $brands=$m_brand->get_list($lan1,$lan2,['p.id'=>['in',$brand_ids]]);
        return ['page'=>$page,'list'=>$list,'brands'=>$brands,'shops'=>$shops];
        
    }
    
    
}

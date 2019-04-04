<?php
 
namespace app\goods\model;

use think\Model;
use think\Db;
class GoodsModel extends Model
{
    /**
     * 获取几个产品
     * @param number $lan
     * @param number $lan1
     * @param array $where
     * @param number $limit
     * @return array
     */
    public function get_limit($lan=1,$lan1=1,$where=['p.status'=>2],$limit=0){
     
        $field='p.id,p.pic,p.pic0,p.store_num,val.name as name';
        $order='p.sort asc,p.shop asc';
        if(empty($limit)){
            $list=$this
            ->alias('p')
            ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->column($field);
        }else{
            $list=$this
            ->alias('p')
            ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->column($field);
        }
        
        if(empty($list)){
            if(empty($limit)){
                $list=$this
                ->alias('p')
                ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->column($field);
            }else{
                $list=$this
                ->alias('p')
                ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->column($field);
            }
        }
        
        return $list;
    }
    /**
     * 获取产品详情
     * @param number $id
     * @param number $lan
     * @param number $lan1 
     * @return array
     */
    public function get_one($id,$lan=1,$lan1=1){
        
        $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
        ',p.box,p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
        $where=['p.id'=>$id];
        $info=$this
        ->alias('p')
        ->field($field)
        ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where) 
        ->find();
        if(empty($info)){
            $info=$this
            ->alias('p')
            ->field($field)
            ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where) 
            ->find();
        }
        if(empty($info)){
            return null;
        }
        
        $info=$info->getData();
        //店铺信息
        $value='val.name';
        $where=['p.id'=>$info['shop']];
        $info['shop_name']=Db::name('shop')
        ->alias('p') 
        ->join('cmf_shop_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where)
        ->value($value);
        if(empty($info['shop_name'])){
            $info['shop_name']=Db::name('shop')
            ->alias('p')
            ->join('cmf_shop_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->value($value);
        }
        //数据手册
        $m_file=new GoodsFileModel();
        $info['files']=$m_file->get_all_by_id($id);
       
        //规格
        $m_param=new GoodsParamModel();
        $info['params']=$m_param->get_params_by_goods($id,$lan,$lan1);
        
        //阶梯价格
        $m_price=new GoodsPriceModel();
        $info['prices']=$m_price->get_all($id);
        
        //获取美元汇率
        $rate=Db::name('shop')->where('id',1)->value('rate');
        $info['price1']=round($info['price1'],5);
        $info['price2']=round($info['price1']*$rate,5);
        foreach($info['prices']as $k=>$v){
            $v['price1']=round($v['price1'],5);
            $v['price2']=round($v['price1']*$rate,5);
            $info['prices'][$k]=$v;
        }
       
        return $info;
    }
    /**
     * 分页产品信息
     * @param number $lan
     * @param number $lan1 
     * @param array $where
     * @param array $data
     * @param string $order 
     * @return array['goods_list'=>$goods_list,'page'=>$page,'file_list'=>$file_list,'price_list'=>$price_list]
     */
    public function goods_page($lan1,$lan2,$where,$data,$order)
    { 
        $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
            ',p.box,p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
       
        $list=$this
        ->alias('p')
        ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
        ->where($where)
        ->field($field)
        ->paginate(10);
        
        // 获取分页显示
        $page = $list->appends($data)->render();
        
        $goods_ids=[];
        $goods_list=[];
        //阶梯价格
        $price_list=[];
        //产品文件
        $file_list=[];
        if(!empty($list)){
            //获取美元汇率
            $rate=Db::name('shop')->where('id',1)->value('rate');
            //得到供应商和品牌id
            foreach($list as $k=>$v){ 
                $goods_ids[]=$v['id']; 
                //价格计算
                $v['price1']=round($v['price1'],5);
                $v['price2']=round($v['price1']*$rate,5);
                $goods_list[$v['id']]=$v;
            }
             
            //阶梯价格
            $m_price=new GoodsPriceModel();
            $price_list=$m_price->get_all_by_pids($goods_ids);
            foreach($price_list as $k=>$v){
                foreach($v as $kk=>$vv){
                    $vv['price1']=round($vv['price1'],5);
                    $vv['price2']=round($vv['price1']*$rate,5);
                    $price_list[$k][$kk]=$vv;
                }
               
            }
            //产品文件
            $m_file=new GoodsFileModel();
            $file_list=$m_file->get_all_by_ids($goods_ids);
        }
         
        return ['goods_list'=>$goods_list,'page'=>$page,'file_list'=>$file_list,'price_list'=>$price_list];
    }
    /**
     * 分页产品信息
     * @param number $lan
     * @param number $lan1
     * @param array $ids产品ids 
     * @return array['goods_list'=>$goods_list,'page'=>$page,'file_list'=>$file_list,'price_list'=>$price_list]
     */
    public function goods_list($lan1,$lan2,$ids)
    {
       /*  $field='p.id,p.code,p.pic,p.box,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2'.
            ',p.goods_time1,p.goods_time2,p.shop,p.weight,p.size,p.is_rohs'.
            ',p.box,p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory'; */
        $field='p.*,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
        
        $where=['p.id'=>['in',$ids]];
        $list=$this
        ->alias('p')
        ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
        ->where($where)
        ->column($field);
        if(empty($list)){
            $list=$this
            ->alias('p')
            ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan2)
            ->where($where)
            ->column($field);
         }
       
        //阶梯价格
        $price_list=[]; 
        if(empty($list)){
          return ['goods_list'=>$list,'price_list'=>$price_list];
        }
        //获取美元汇率
        $rate=Db::name('shop')->where('id',1)->value('rate');
        //得到供应商和品牌id
        foreach($list as $k=>$v){ 
            //价格计算 
            $list[$k]['price2']=round($v['price1']*$rate,5); 
        }
            
        //阶梯价格
        $m_price=new GoodsPriceModel();
        $price_list=$m_price->get_all_by_pids($ids);
        foreach($price_list as $k=>$v){
            foreach($v as $kk=>$vv){ 
                $price_list[$k][$kk]['price2']=round($vv['price1']*$rate,5); 
            } 
        } 
        return ['goods_list'=>$list,'price_list'=>$price_list];
    }
    
    public function goods_count($nums,$lan1,$lan2,$price_type){
       
        $goods_ids=array_keys($nums); 
       
        $res=$this->goods_list($lan1,$lan2,$goods_ids); 
        $goods_list=$res['goods_list'];
        $price_list=$res['price_list'];
        $shop_ids=[];
        $brand_ids=[];
        //累加产品数量,金额,重量
        $count=[
            'num'=>0,
            'money'=>0,
            'weight'=>0,
            'size'=>0, 
            'brand_ids'=>[]
        ];
      
        //产品信息，获取价格
        foreach($goods_list as $k=>$v){
            
            $v['price']=$v['price'.$price_type];
            $v['goods_time']=$v['goods_time'.$price_type];
            $v['order_num']=$nums[$k];
            
            if(isset($price_list[$k])){
                foreach($price_list[$k] as $kk=>$vv){
                    if($v['num']>=$vv['num']){
                        $v['price']=$vv['price'.$price_type];
                    }
                }
            }
            $v['order_price']=round($nums[$k]* $v['price'],2);
            
            $goods_list[$k]=$v; 
            $count['brand_ids'][$v['brand']]=$v['brand'];
            //累加产品数量,金额,重量
            $count['num']+=$v['order_num'];
            $count['money']+=$v['order_price'];
            $count['weight']+=$nums[$k]*$v['weight'];
            $count['size']+=$nums[$k]*$v['size'];
        }
        $count['num']=intval($count['num']);
        $count['money']=round($count['money'],2);
        $count['weight']=round($count['weight'],2);
        $count['size']=round($count['size'],2);
        return ['goods_list'=>$goods_list,'price_list'=>$price_list,'count'=>$count];
    }
}

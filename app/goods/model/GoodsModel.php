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
        ',p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
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
     * @param unknown $where
     * @param unknown $order
     * @param unknown $field
     * @param unknown $join
     * @return unknown
     */
    public function goods_page($lan1,$lan2,$where,$data,$order)
    { 
        $field='p.id,p.code,p.pic,p.pic0,p.store_num,p.store_code,p.store_sure,p.brand,p.price1,p.price2,p.goods_time1,p.goods_time2,p.shop'.
            ',p.num_min,p.num_times,p.num_one,val.name as name,val.dsc as dsc,val.production_code as production_code,val.production_factory as production_factory';
       
        $list=$this
        ->alias('p')
        ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
        ->where($where)
        ->field($field)
        ->paginate(2);
        
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
}

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
        $list=$this
        ->alias('p')
        ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan)
        ->where($where)
        ->order($order)
        ->column($field);
        if(empty($list)){
            $list=$this
            ->alias('p')
            ->join('cmf_goods_val val','val.pid=p.id and val.lid='.$lan1)
            ->where($where)
            ->order($order)
            ->column($field);
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
        
        return $info;
    }
}

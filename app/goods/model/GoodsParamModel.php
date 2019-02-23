<?php
 
namespace app\goods\model;

use think\Model;
use think\Db;
class GoodsParamModel extends Model
{
    /**
     * 获取所有参数 
     */
    public function get_all(){
        $params=$this->where('status',2)->order('sort asc')->column('id,name,is_lan,sort,dsc');
        return $params;
    }
    /**
     * 获取模板关联的参数
     * @param number $tid
     */
    public function get_params_by_template($tid,$lid=0){
        $res=Db::name('goods_template_param')
        ->alias('tp')
        ->join('cmf_goods_param p','p.id=tp.param')
        ->where('tp.template',$tid)
        ->order('tp.sort asc')
        ->column('tp.param,tp.sort,tp.is_search,p.is_lan,p.name as param_name');
        return $res;
    }
    /**
     * 获取产品的规格参数
     * @param unknown $goods
     * @param number $lan
     * @param number $lan1
     */
    public function get_params_by_goods($goods,$lan=1,$lan1=1){
        $where=[
            'pg.goods'=>$goods,
            'pg.lid'=>['in',[0,$lan]]
        ];
        $field='pg.*,pv.val as name';
        $order='pg.sort asc';
        $params=Db::name('goods_param_goods')
        ->alias('pg') 
        ->join('cmf_goods_param_val pv','pv.pid=pg.param and pv.lid='.$lan)
        ->where($where)
        ->order($order)
        ->column($field);
        if(empty($params)){
            $where=[
                'pg.goods'=>$goods,
                'pg.lid'=>['in',[0,$lan1]]
            ];
            $params=Db::name('goods_param_goods')
            ->alias('pg')
            ->join('cmf_goods_param_val pv','pv.pid=pg.param and pv.lid='.$lan1)
            ->where($where)
            ->order($order)
            ->column($field);
        }
        return $params;
    }
    
}

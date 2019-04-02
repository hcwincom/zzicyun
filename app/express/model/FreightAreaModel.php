<?php
 
namespace app\express\model;

use think\Model;
use think\Db;
/**
 * 快递费用 
 *
 */
class FreightAreaModel extends Model
{
     
    /**
     * 获取全部费用设置
     * @return array
     */
    public function get_all($freight){
        
        $where=[
            'freight'=>$freight, 
        ];
       
        $list=$this->where($where)->column('*','area');
        
        return $list;
         
    }
     
    
    /**
     *  按所选快递和目的地计费
     * @param int $freight快递公司
     * @param int $city目的地
     * @param int $count_weight订单重量
     * @param int $count_size订单体积
     * @param number $count_money订单金额 
     * @return float 运费
     */
    public function get_fee($freight,$city,$count_weight,$count_size,$count_money,$money_type=1){
        //先获取城市所在区域
        $where=[
            'city'=>$city,
        ];
        $areas=Db::name('area_city')->where($where)->column('area');
        if(empty($areas)){
            return -1;
        }
        //获取计费规则
        $where=[
            'freight'=>$freight,
            'area'=>['in',$areas],
        ];
        $price=$this->where($where)->find();
        $price=$price->getData();
        //美元换算
        if($money_type>1){
            $rate=Db::name('shop')->where('id',1)->value('rate');
            $price['price0']=$price['price0']*$rate;
            $price['price1']=$price['price1']*$rate;
            $price['price1']=$price['price2']*$rate; 
        }
      
         
        //体积比较
        if($price['size_rate']>0){
            $weight_tmp=$count_size/$price['size_rate'];
            if($weight_tmp>$count_weight){
                $count_weight=$weight_tmp;
            }
        }
        //达到免邮标准
        if($count_money>=$price['price0']){
            if($count_weight<=$price['weight0']){
                $fee=0;
            }else{
                //超重计费
                $fee=round(ceil(($count_weight-$price['weight0'])/$price['weight2'])*$price['price2'],2); 
            }
        }else{
            //首重
            if($count_weight<=$price['weight1']){
                $fee=$price['price1'];
            }else{
                //超重计费
                $fee=$price['price1']+ceil(($count_weight-$price['weight1'])/$price['weight2'])*$price['price2']; 
            }
        }
        return round($fee,2);
       
    }
    
    
}

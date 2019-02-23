<?php
 
namespace app\goods\model;

use think\Model; 
class GoodsPriceModel extends Model
{
    public function get_all($pid){
        $res=$this->where('pid',$pid)->order('num asc')->column('');
        return $res;
    }
     
}

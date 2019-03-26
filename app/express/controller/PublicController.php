<?php
 
namespace app\express\controller;
 
use app\common\controller\DeskBaseController; 
use think\Db; 
use app\express\model\AreaModel;
 
class PublicController extends DeskBaseController
{
 
    //获取地址
    public function citys(){
        $fid=$this->request->param('fid',0,'intval');
        $m_area=new AreaModel();
        $list=$m_area->get_all($fid);
        $this->success('ok','',$list);
    }
    //获取一个城市的区号，邮编
    public function city_one()
    {
        $id=$this->request->param('id',0,'intval');
        if($id<1){
            $this->error('数据错误');
        }
        $where=[
            'id'=>$id,
        ];
        $city=Db::name('area')->field('id,name,code,postcode,fid')->where($where)->find();
        $this->success('ok','',['name'=>$city['name'],'city_code'=>$city['code'],'postcode'=>$city['postcode'],'fid'=>$city['fid']]);
    }
   
    /**
     * 查询快递 
     */
    public function express_query(){
        
        $express=$this->request->param('express',0,'intval');
        $m=$this->m;
        if(empty($express)){
            $freight=$this->request->param('freight',0,'intval');
            $express=$m
            ->where('id',$freight)
            ->value('express');
            if(empty($express)){
                $this->error('未选择快递公司');
            }
        }
        
        $code=Db::name('express')->where('id',$express)->value('code');
        if(empty($code)){
            $this->error('没有快递类型编码');
        }
        $no=$this->request->param('no');
        if(empty($no)){
            $this->error('没有单号');
        }
        $url='https://www.kuaidi100.com/chaxun?';
        header('location:'.$url.'com='.$code.'&nu='.$no);
    }
     
}

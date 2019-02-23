<?php
 
namespace app\goods\controller;

 
use app\common\controller\AdminBase0Controller; 
use think\Db; 
use app\goods\model\GoodsModel;
use app\goods\model\GoodsParamModel;
/*
 * 产品页面的ajax  */ 
class GoodsajaxController extends AdminBase0Controller
{ 
    public function _initialize()
    { 
        parent::_initialize();
        //计算小数位
        bcscale(2); 
    }
     
   
    /**
     * 产品参数设置 
     */
    public function template_set(){
        $t_id=$this->request->param('template',0,'intval');
        if($t_id<=0){
            $this->success('no');
        }
        $m_param=new GoodsParamModel();
        $params_ids=$m_param->get_params_by_template($t_id);
         
        if(empty($params_ids)){
            $this->success('no');
        }
        
        $this->success('ok','',$params_ids);
    }
    
    
    /**
     * 产品价格模板确认 
     */
    public function price_set(){
        $t_id=$this->request->param('t_id',0,'intval');
        $price_in=$this->request->param('price_in',0);
        if($t_id<=0 || $price_in<=0){
            $this->success('no');
        }
        $price_in=round($price_in,2);
        //按模板规则计算得到各种价格，暂时不写
        $data=[];
        $data['price_cost']=$price_in;
        $data['price_min']=$price_in;
        $data['price_range1']=$price_in;
        $data['price_range2']=$price_in;
        $data['price_range3']=$price_in;
        $data['price_dealer1']=$price_in;
        $data['price_dealer2']=$price_in;
        $data['price_dealer3']=$price_in;
        $data['price_trade']=$price_in;
        $data['price_factory']=$price_in;
        $this->success('ok','',$data);
        
    }
     /*  
      * 收藏
      * */
    public function goods_collect(){
        $pid=$this->request->param('pid',0,'intval');
        $admin=$this->admin;
        $where=[
            'pid'=>$pid,
            'uid'=>$admin['id'],
        ];
        $m_collect=Db::name('goods_collect');
        $tmp=$m_collect->where($where)->find();
        $time=time();
        if(empty($tmp)){
            $data=[
                'pid'=>$pid,
                'uid'=>$admin['id'],
                'type'=>4,
                'ctime'=>$time,
                'time'=>$time,
            ];
            $m_collect->insert($data);
            $this->success('已收藏');
        }else{
            $data=[ 
                'time'=>$time,
            ];
            $m_collect->where('id',$tmp['id'])->update($data);
            $this->success('已更新时间');
        }
    }
   
    
    //根据分类和技术模板得到二级分类和产品
    public function goods_get_by_template(){
        $cid0=$this->request->param('cid0',0,'intval');
        $tid=$this->request->param('tid',0,'intval');
        if($cid0<=0){
            $this->error('请选择一级分类');
        }
        if($tid<=0){
            $this->error('请选择技术模板');
        }
        $where_cate=[
            'fid'=>$cid0,
            'status'=>2,
        ];
        $cate=Db::name('cate')->where($where_cate)->column('id,name');
        if(empty($cate)){
            $this->error('没有找到符合条件的产品');
        }
        $where_goods=[
            'cid0'=>$cid0,
            'status'=>2,
            'template'=>$tid,
        ];
        $goods=Db::name('goods')->where($where_goods)->column('id,cid,name');
        if(empty($goods)){
            $this->error('没有找到符合条件的产品');
        }
        $where_param=[
            'tp.t_id'=>$tid,
            'p.status'=>2,
        ];
        $params=Db::name('template_param')
        ->alias('tp')
        ->join('cmf_param p','p.id=tp.p_id')
        ->where($where_param)->column('p.id,p.name');
        $this->success('ok','',['cate'=>$cate,'goods'=>$goods,'params'=>$params]);
        
    }
    //获取分类下所有产品
    public function goods(){
        $cid=$this->request->param('cid');
        $where=[
            'cid'=>$cid,
            'status'=>2,
        ];
        $admin=$this->admin; 
        $where['shop']=($admin['shop']==1)?2:$admin['shop'];
        
        $goods=Db::name('goods')->where($where)->column('id,name,code');
        $this->success('ok','',$goods);
    }
    //获取产品的参数值
    public function get_param_by_goods(){
        $pid=$this->request->param('pid');
        $goods=Db::name('goods')->where('id',$pid)->find();
        if(empty($goods)){
            $this->error('没有找到符合条件的产品');
        }
        $units=config('units');
        $unit=$units[$goods['unit']];
        $goods['unit_name']=implode(',', $unit);
        $param=Db::name('goods_param')->where('pid',$pid)->column('param_id,value');
        $this->success('ok','',['goods'=>$goods,'param'=>$param]);
    }
    /*
     * 获取分类 */
    public function get_cates()
    {
        
        $where1=[
            'status'=>2, 
            'fid'=>0,
        ]; 
        $list1=Db::name('cate')->where($where1)->order('code_num asc')->column('id,name,code,type,fid','code_num');
        $where2=[
            'status'=>2,
            'fid'=>['gt',0],
        ];
        $list2=Db::name('cate')->where($where2)->order('code asc')->column('id,name,code,type,fid','code');
        $this->success('ok','',['list1'=>$list1,'list2'=>$list2]);
    }
    /*
     * 根据cid获取产品 */
    public function get_goods()
    {
        $cid=$this->request->param('cid');
        $where=[
            'cid'=>$cid,
            'status'=>2,
        ];
        $admin=$this->admin;
        $where['shop']=($admin['shop']==1)?2:$admin['shop']; 
        $goods=Db::name('goods')->where($where)->column('id,name');
        $this->success('ok','',$goods);
    }
    /*
     * 根据id获取产品 */
    public function get_goods_info()
    {
        $id=$this->request->param('id');
        $shop=$this->request->param('shop');
        if(empty($shop)){
            $admin=$this->admin;
            $shop=($admin['shop']==1)?2:$admin['shop'];
        }
        
        $m_goods=new GoodsModel();
        $goods=$m_goods->goods_info($id,$shop);
        $this->success('ok','',$goods);
    }
    
   /*  下载产品文件*/
    public function file_load(){
        $data=$this->request->param();
        $id=intval($data['id']);
        if(empty($data['type'])){
            $file=Db::name('goods_file')->where('id',$id)->find();
        }else{
            $change=Db::name('edit_info')->where('eid',$id)->value('content');
            $change=json_decode($change,true);
            if(isset($change['files']['add'][$data['type']])){
                $file=$change['files']['add'][$data['type']];
            }elseif(isset($change['files']['edit'][$data['type']])){
                $file=$change['files']['edit'][$data['type']];
            }else{
                $this->error('文件不存在');
            }
        }
         
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

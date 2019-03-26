<?php
 
namespace app\common\controller;
use cmf\controller\HomeBaseController;
use think\Db; 
 
use think\View;
use app\notice\model\NoticeConfValModel;
use app\goods\model\GoodsModel;
 
class DeskBaseController extends HomeBaseController
{
    protected $lan1;
    protected $lan2;
    public function _initialize()
    {
        parent::_initialize();
        
        View::share('zzsite', config('zzsite'));
        View::share('image_url', cmf_get_image_url(''));
         
        $lan1=1;
        //没有数据找默认
        $lan2=1;
        session('lan1',$lan1);
        session('lan2',$lan2);
        $this->lan1=$lan1;
        $this->lan2=$lan2;
        $notice_json=session('notice'.$lan1);
        if(empty($notice_json) || 1){ 
            //获取所有notice信息 
            $notices=Db::name('notice')
            ->alias('notice')
            ->join('cmf_notice_cate cate','notice.cid=cate.id')
            ->join('cmf_notice_val val','notice.id=val.pid and lid='.$lan1)
            ->where('notice.status',2)
            ->column('notice.id,notice.name,cate.name as cname,val.val');
            //没有数据找默认
            if(empty($notices)){ 
                $notices=Db::name('notice')
                ->alias('notice')
                ->join('cmf_notice_cate cate','notice.cid=cate.id')
                ->join('cmf_notice_val val','notice.id=val.pid and lid='.$lan2)
                ->where('notice.status',2)
                ->column('notice.id,notice.name,cate.name as cname,val.val');
            }
            $notice=[];
            foreach($notices as $k=>$v){
                $notice[$v['cname']][$v['name']]=$v['val'];
            }
            //获取配置信息
            $m_conf=new NoticeConfValModel();
            $list0=$m_conf->get_all_by_lan($lan1,$lan2);
            foreach($list0 as $k=>$v){
                $notice[$k]=$v;
            }
           //获取底部的文章名称cid=[7,8,9,10]
            $where=['id'=>['in',[7,8,9,10]]];
            
            
            //底部图片
//             9-底部微信二维码	foot_qr,1
//             8-底部图片	foot_img,6
            $where=['status'=>2,'cid'=>['in',[8,9]]];
            $foot_pics=Db::name('banner')->where($where)->order('sort asc')->column('id,cid,url,pic');
            foreach($foot_pics as $k=>$v){
                if($v['cid']==9){
                    $notice['foot_qr'][$v['id']]=$v;
                }else{
                    if(empty($notice['foot_img'])){
                        $notice['foot_img'][$v['id']]=$v;
                    }else{
                        if(count($notice['foot_img'])<6){
                            $notice['foot_img'][$v['id']]=$v;
                        }
                    } 
                }
            }
            $notice_json=json_encode($notice);
            session('notice'.$lan1,$notice); 
        } else{
            $notice=json_decode($notice_json,true);
        }
        $company=session('company1');
        if(empty($company)){
            $company=Db::name('shop')->where('id',1)->find();
            session('company',$company);
        }
        //热门产品
        $m_goods=new GoodsModel();
        $goods=$m_goods->get_limit($lan1,$lan2,['status'=>2],3);
        $this->assign('goods_hot',$goods);
        View::share('company',$company);
        View::share('notice',$notice);
        View::share('notice_json',$notice_json);
        $this->assign('html',$this->request->action());
        $this->assign('module',$this->request->module());
        $this->assign('controller',$this->request->controller());
      
       
    }


}
<?php
 
namespace app\common\controller;
use cmf\controller\HomeBaseController;
use think\Db; 
 
use think\View;
 
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
            //获取所有信息 
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
        View::share('company',$company);
        View::share('notice',$notice);
        View::share('notice_json',$notice_json);
        $this->assign('html',$this->request->action());
    }


}
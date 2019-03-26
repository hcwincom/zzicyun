<?php
 
namespace app\user\controller;

 
use think\Db; 
use app\goods\model\GoodsCateModel;
use app\common\controller\AdminInfoController;
/* 用户行业 */
class AdminUserIndustryController extends AdminInfoController
{
    
    public function _initialize()
    {
        parent::_initialize();
       
        $this->flag='用户行业';
        $this->table='user_industry';
        $this->m=Db::name('user_industry');
        $this->base=['name'=>'str','sort'=>'int','dsc'=>'str'];
         
        $this->isshop=0;
        $this->islan=1;
        $this->vals=[
            'name'=>'多语言名称'
        ];
        $this->assign('flag','用户行业');
         
    }
    /**
     * 用户行业列表
     * @adminMenu(
     *     'name'   => '用户行业列表',
     *     'parent' => 'user/AdminIndex/default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 81,
     *     'icon'   => '',
     *     'remark' => '用户行业列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        
        parent::index();
        return $this->fetch();
    }
    
   
    /**
     * 用户行业添加
     * @adminMenu(
     *     'name'   => '用户行业添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        parent::add();
        
        return $this->fetch();
    }
    /**
     * 用户行业添加do
     * @adminMenu(
     *     'name'   => '用户行业添加do',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业添加do',
     *     'param'  => ''
     * )
     */
    public function add_do()
    {
        parent::add_do();
    }
    
    /**
     * 用户行业详情
     * @adminMenu(
     *     'name'   => '用户行业详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        parent::edit();
        return $this->fetch();
    }
    /**
     * 用户行业状态审核
     * @adminMenu(
     *     'name'   => '用户行业状态审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业状态审核',
     *     'param'  => ''
     * )
     */
    public function review()
    {
        parent::review();
    }
    /**
     * 用户行业状态批量同意
     * @adminMenu(
     *     'name'   => '用户行业状态批量同意',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业状态批量同意',
     *     'param'  => ''
     * )
     */
    public function review_all()
    {
        parent::review_all();
    }
    /**
     * 用户行业禁用
     * @adminMenu(
     *     'name'   => '信息状态禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '信息状态禁用',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        parent::ban();
    }
    /**
     * 用户行业信息状态恢复
     * @adminMenu(
     *     'name'   => '用户行业信息状态恢复',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业信息状态恢复',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        parent::cancel_ban();
    }
    /**
     * 用户行业编辑提交
     * @adminMenu(
     *     'name'   => '用户行业编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_do()
    {
        parent::edit_do();
    }
    /**
     * 用户行业编辑列表
     * @adminMenu(
     *     'name'   => '用户行业编辑列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业编辑列表',
     *     'param'  => ''
     * )
     */
    public function edit_list(){
        parent::edit_list();
        return $this->fetch();  
    }
    
    /**
     * 用户行业审核详情
     * @adminMenu(
     *     'name'   => '用户行业审核详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业审核详情',
     *     'param'  => ''
     * )
     */
    public function edit_info()
    {
        parent::edit_info();
        return $this->fetch();
    }
    /**
     * 用户行业信息编辑审核
     * @adminMenu(
     *     'name'   => '用户行业编辑审核',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业编辑审核',
     *     'param'  => ''
     * )
     */
    public function edit_review()
    {
        parent::edit_review();
    }
    /**
     * 用户行业编辑记录批量删除
     * @adminMenu(
     *     'name'   => '用户行业编辑记录批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业编辑记录批量删除',
     *     'param'  => ''
     * )
     */
    public function edit_del_all()
    {
        parent::edit_del_all();
    }
    /**
     * 用户行业批量删除
     * @adminMenu(
     *     'name'   => '用户行业批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10,
     *     'icon'   => '',
     *     'remark' => '用户行业批量删除',
     *     'param'  => ''
     * )
     */
    public function del_all()
    {
         
        parent::del_all();
        
    }
    
    public function cates($type=3){
        parent::cates($type);
         
        
    }
    /*删除前检查  */
    public function del_before($ids){
        $m=$this->m; 
        //检查是否有品牌
        $where=['cid'=>['in',$ids]];
        $tmp=Db::name('goods_brand')->where($where)->find();
        if(!empty($tmp)){
            return ('品牌分类'.$tmp['cid'].'下有品牌'.$tmp['name']);
        }
        return 1;
    }
    
   
}

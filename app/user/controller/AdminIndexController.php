<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\notice\model\NoticeConfValModel;
use app\user\model\UserIndustryModel;
use app\score\model\ScoreUserModel;
use app\user\model\UserBalanceModel; 
use app\user\model\UserCreditModel;
use app\money\model\CreditGetModel;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $where   = ['user_type'=>2];
        $admin=$this->admin;
        if($admin['shop']!=1){
            $where['shop']= $admin['shop'];
        }
        $request = input('request.');

        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $keywordComplex['user_login|user_nickname|user_email|mobile']    = ['like', "%$keyword%"];
        }
        $usersQuery = Db::name('user');

        $list = $usersQuery->whereOr($keywordComplex)->where($where)->order("create_time DESC")->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }
    /**
     * 用户信息
     * @adminMenu(
     *     'name'   => '用户信息',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '用户信息',
     *     'param'  => ''
     * )
     */
    public function info()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $lan1=1;
        $lan2=1;
        $user = DB::name('user')
        ->where(["id" => $id])
        ->find();
        //会员等级user_rates
        $m_conf=new NoticeConfValModel();
        $user_rates=$m_conf->get_one($lan1,$lan2,'user_rates');
        $user['rate_name']=empty($user_rates[$user['user_rate']])?$user['user_rate']:$user_rates[$user['user_rate']];
        //用户类型
        $user_cates=$m_conf->get_one($lan1,$lan2,'user_cates');
        $user['cate_name']=empty($user_cates[$user['user_cate']])?$user['user_cate']:$user_cates[$user['user_cate']];
        //用户行业
        $m_industry=new UserIndustryModel();
        $user['industry_name']=$m_industry->get_name($lan1, $lan2,$user['user_industry']);
        $this->assign('user',$user);
        
        return $this->fetch();
    }
    /**
     * 后台用户充值
     * @adminMenu(
     *     'name'   => '后台用户充值',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '后台用户充值',
     *     'param'  => ''
     * )
     */
    public function balance()
    {
        $id    = $this->request->param('id', 0, 'intval');
        
        $user = DB::name('user')
        ->where(["id" => $id])
        ->find();
        
        $this->assign('user',$user);
        
        return $this->fetch();
    }
    /**
     * 后台用户充值执行
     * @adminMenu(
     *     'name'   => '后台用户充值执行',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '后台用户充值执行',
     *     'param'  => ''
     * )
     */
    public function balance_do()
    {
       
        
        $data    = $this->request->param();
        foreach($data as $k=>$v){
            $data[$k]=intval($v);
        }
        $uid= $data['id'];
        if(empty($uid)){
            $this->error('数据错误');
        }
        
        $admin=$this->admin;
        //授信额度
        $m_user_credit=new CreditGetModel();
        $m_user_credit->startTrans();
        $dsc='管理员后台充值';
        if($data['credit1'] != 0){
            $m_user_credit->num_do($uid,$data['credit1'],$dsc,1,$admin['id']);
        }
        if($data['credit2'] != 0){
            $m_user_credit->num_do($uid,$data['credit2'],$dsc,1,$admin['id']);
        } 
        //积分充值
        if($data['score']!=0){
            $m_su=new ScoreUserModel();
            $m_su->score_do($uid, 'score_add',0,$data['score'],$admin['id']);
        }
        
        //余额充值
        if($data['balance']!=0){
            $m_user_balance=new UserBalanceModel();
            $m_user_balance->balance_do($uid,$data['balance'],$dsc,$admin['id']);
        } 
        $m_user_credit->commit();
        $this->success('充值成功');
    }
    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $result = Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 0);
            if ($result) {
                $this->success("会员拉黑成功！", "adminIndex/index");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
}

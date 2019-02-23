<?php
 
namespace app\admin\controller;

 
use cmf\controller\AdminBaseController; 
use think\Db; 
 /* 旧数据处理 */
class OldController extends AdminBaseController
{
    private $dbconfig;
    private $db_old;
    private $corrects;
    private $where_corrects;
    public function _initialize()
    {
        parent::_initialize();
        //链接数据库
        $db=config('database'); 
        $this->dbconfig=[
            'host'=>$db['hostname'],
            'user'=>$db['username'],
            'psw'=>$db['password'],
            'dbname'=>$db['database'],
            'port'=>$db['hostport'],
        ];
       
        $aid=session('ADMIN_ID');
        if($aid!=1){
            $this->error('开发者功能，不要操作');
        }
        $this->corrects=['status'=>2,'aid'=>1,'rid'=>1,'atime'=>time(),'rtime'=>time(),'time'=>time()];
        $this->where_corrects=['rid'=>0];
        $this->db_old= config('db_old');
    }
     
    /**
     * 旧数据同步
     * @adminMenu(
     *     'name'   => '旧数据同步',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '旧数据同步',
     *     'param'  => ''
     * )
     */
    public function index()
    { 
        $list=[
            
            '清空编辑记录'=>url('store_clear'),
            '清空菜单和权限'=>url('menu_clear'),
           
        ];
        $this->assign('list',$list);
        return $this->fetch();
    }
     
    //发货记录
    public function store_clear(){
        set_time_limit(300);
       
        //订单主体
        $m_new=Db::name('msg');
        
        $m_new->execute('truncate table cmf_edit'); 
        $m_new->execute('truncate table cmf_edit_info'); 
        $m_new->execute('truncate table cmf_msg'); 
        $m_new->execute('truncate table cmf_msg_txt'); 
        
        //产品关联数据
        
        echo ('end');
    }
    /**
     * 清空菜单和权限
     */
    public function menu_clear(){
        set_time_limit(300);
        
        //订单主体
        $m_new=Db::name('admin_menu');
        
        //先截取旧数据
        $m_new->execute('truncate table cmf_admin_menu');
        $m_new->execute('truncate table cmf_auth_access');
        $m_new->execute('truncate table cmf_auth_rule');
        
        echo ('end');
    }
    
}

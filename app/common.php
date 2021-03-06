<?php
// | Copyright (c) 2018-2019 http://zz.zheng11223.top All rights reserved.
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// | Author: infinitezheng<infinitezheng@qq.com>
use think\Config;
use think\Db;
use think\Url;
 
// 应用公共文件

 
/**
 * 店铺搜索确认
 * @param array $admin
 * @param array &$data
 * @param array &$where 
 * @param string $field
 * @return array 1
 */
function zz_shop($admin,&$data,&$where,$field='shop'){
    if($admin['shop']==1){
        if(empty($data['shop'])){
            $data['shop']=0; 
        }else{
            $where[$field]=$data['shop']; 
        }
    }else{
        $where[$field]=$admin['shop']; 
    }
    return 1;
}
/**
 * 生成订单号
 * @param number $aid
 * @param string $str
 * @return string
 */
 function order_sn($aid=1,$str=''){
     return $str.date('YmdHis').$aid;
 }

/**
 * 操作后记录和通知
 */
function zz_action($data_action,$data=[]){
    
    
    Db::name('action')->insert($data_action);
    $m_msg_txt=Db::name('msg_txt');
    $data_msg=[];
    $uids=[];
    $m_user=Db::name('user');
    $dsc=strstr($data_action['action'],'(',true);
    //要获取需要发送消息的对象 
    switch($data_action['type']){
        case 'add':
        case 'edit':
            if(empty($data['department'])){
                $data=session('admin');
            }
            //如果是添加和编辑要通知经理
            $where=[
            'shop'=>['eq',$data_action['shop']],
            'department'=>['in',[1,$data['department']]],
            'job'=>['eq',1],
            ];
            $uids=$m_user->where($where)->column('id');
            break;
        case 'review':
        case 'edit_review':
            //审核要通知编辑人员
            $uids=[$data['aid']];
            break;
        case 'review_all':
            //批量审核pids
            $list=Db::name($data_action['table'])->where('id','in','('.$data['pids'].')')->column('id,aid,name');
            //不同的人，不同的信息
            foreach($list as $k=>$v){
                $data_msg_txt=[
                    'time'=>$data_action['time'],
                    'dsc'=>$dsc.$v['id'].'-'.$v['name'],
                    'type'=>$data_action['type'],
                    'link'=>url('edit',['id'=>$v['id']]),
                ];
                $msg_id=$m_msg_txt->insertGetId($data_msg_txt);
                $data_msg[]=[
                    'uid'=>$v['aid'],
                    'aid'=>$data_action['aid'],
                    'msg'=>$msg_id,
                    'shop'=>$data_action['shop'],
                ];
            }
            break;
        case 'edit_del':
            //批量删除编辑记录
            //暂时只有编辑人员 
            $list=Db::name('edit')
            ->alias('e')
            ->join('cmf_'.$data_action['table'].' p','p.id=e.pid')
            ->where('e.id','in','('.$data['eids'].')')
            ->column('e.id as eid,p.id,e.aid,p.name');
            //不同的人，不同的信息
            foreach($list as $k=>$v){
                $data_msg_txt=[
                    'time'=>$data_action['time'],
                    'dsc'=>$dsc.$v['id'].'-'.$v['name'],
                    'type'=>$data_action['type'],
                    'link'=>url('edit',['id'=>$v['id']]),
                ];
                $msg_id=$m_msg_txt->insertGetId($data_msg_txt);
                $data_msg[]=[
                    'uid'=>$v['aid'],
                    'aid'=>$data_action['aid'],
                    'msg'=>$msg_id,
                    'shop'=>$data_action['shop'],
                ];
            }
            break;
        case 'del':
            //批量 删除 
            $list=Db::name($data_action['table'])->where('id','in','('.$data['pids'].')')->column('id,aid,name');
            //不同的人，不同的信息
            foreach($list as $k=>$v){
                $data_msg_txt=[
                    'time'=>$data_action['time'],
                    'dsc'=>$dsc.$v['id'].'-'.$v['name'],
                    'type'=>$data_action['type'],
                    'link'=>'',
                ];
                $msg_id=$m_msg_txt->insertGetId($data_msg_txt);
                $data_msg[]=[
                    'uid'=>$v['aid'],
                    'aid'=>$data_action['aid'],
                    'msg'=>$msg_id,
                    'shop'=>$data_action['shop'],
                ];
            }
            break;
    }
   
    if(empty($data_msg)){ 
        //发送审核信息
        $data_msg_txt=[
            'time'=>$data_action['time'],
            'dsc'=>$data_action['action'],
            'type'=>$data_action['type'],
            'link'=>$data_action['link'],
        ];
        $msg_id=Db::name('msg_txt')->insertGetId($data_msg_txt); 
        foreach($uids as $v){
            $data_msg[]=[
                'uid'=>$v,
                'aid'=>$data_action['aid'], 
                'msg'=>$msg_id,
                'shop'=>$data_action['shop'],
            ];
        } 
    } 
    if(!empty($data_msg)){
        Db::name('msg')->insertAll($data_msg);
    } 
}
/**
 * 操作后记录和通知用户
 */
function zz_action_user($data_action,$data=[]){
    
    
    Db::name('action')->insert($data_action);
    $m_msg_txt=Db::name('msg_txt');
    $data_msg=[];
    $uids=[];
    $m_user=Db::name('user');
    $dsc=strstr($data_action['action'],'(',true);
    //要获取需要发送消息的对象
    switch($data_action['type']){
        case 'add':
        case 'edit':
            if(empty($data['department'])){
                $data=session('admin');
            }
            //如果是添加和编辑要通知经理
            $where=[
                'shop'=>['eq',$data_action['shop']],
                'department'=>['in',[1,$data['department']]],
                'job'=>['eq',1],
            ];
            $uids=$m_user->where($where)->column('id');
            break;
        case 'review':
        case 'edit_review':
            //审核要通知编辑人员
            $uids=[$data['aid']];
            break;
        case 'review_all':
            //批量审核pids
            $list=Db::name($data_action['table'])->where('id','in','('.$data['pids'].')')->column('id,aid,name');
            //不同的人，不同的信息
            foreach($list as $k=>$v){
                $data_msg_txt=[
                    'time'=>$data_action['time'],
                    'dsc'=>$dsc.$v['id'].'-'.$v['name'],
                    'type'=>$data_action['type'],
                    'link'=>url('edit',['id'=>$v['id']]),
                ];
                $msg_id=$m_msg_txt->insertGetId($data_msg_txt);
                $data_msg[]=[
                    'uid'=>$v['aid'],
                    'aid'=>$data_action['aid'],
                    'msg'=>$msg_id,
                    'shop'=>$data_action['shop'],
                ];
            }
            break;
        case 'edit_del':
            //批量删除编辑记录
            //暂时只有编辑人员
            $list=Db::name('edit')
            ->alias('e')
            ->join('cmf_'.$data_action['table'].' p','p.id=e.pid')
            ->where('e.id','in','('.$data['eids'].')')
            ->column('e.id as eid,p.id,e.aid,p.name');
            //不同的人，不同的信息
            foreach($list as $k=>$v){
                $data_msg_txt=[
                    'time'=>$data_action['time'],
                    'dsc'=>$dsc.$v['id'].'-'.$v['name'],
                    'type'=>$data_action['type'],
                    'link'=>url('edit',['id'=>$v['id']]),
                ];
                $msg_id=$m_msg_txt->insertGetId($data_msg_txt);
                $data_msg[]=[
                    'uid'=>$v['aid'],
                    'aid'=>$data_action['aid'],
                    'msg'=>$msg_id,
                    'shop'=>$data_action['shop'],
                ];
            }
            break;
        case 'del':
            //批量 删除
            $list=Db::name($data_action['table'])->where('id','in','('.$data['pids'].')')->column('id,aid,name');
            //不同的人，不同的信息
            foreach($list as $k=>$v){
                $data_msg_txt=[
                    'time'=>$data_action['time'],
                    'dsc'=>$dsc.$v['id'].'-'.$v['name'],
                    'type'=>$data_action['type'],
                    'link'=>'',
                ];
                $msg_id=$m_msg_txt->insertGetId($data_msg_txt);
                $data_msg[]=[
                    'uid'=>$v['aid'],
                    'aid'=>$data_action['aid'],
                    'msg'=>$msg_id,
                    'shop'=>$data_action['shop'],
                ];
            }
            break;
    }
    
    if(empty($data_msg)){
        //发送审核信息
        $data_msg_txt=[
            'time'=>$data_action['time'],
            'dsc'=>$data_action['action'],
            'type'=>$data_action['type'],
            'link'=>$data_action['link'],
        ];
        $msg_id=Db::name('msg_txt')->insertGetId($data_msg_txt);
        foreach($uids as $v){
            $data_msg[]=[
                'uid'=>$v,
                'aid'=>$data_action['aid'],
                'msg'=>$msg_id,
                'shop'=>$data_action['shop'],
            ];
        }
    }
    if(!empty($data_msg)){
        Db::name('msg')->insertAll($data_msg);
    }
}
/**
 * 下载文件
 * @param $file 文件
 * @param $name 文件名
 */
function zz_download($file,$name=''){
    $path='upload/';
    $file=$path.$file;
    if(!is_file($file)){
        $this->error('文件损坏，不存在');
    } 
    $filename=empty($name)?time():$name;
    
    $info=pathinfo($file);
    $ext=$info['extension']; 
    header('Content-type: application/x-'.$ext);
    header('content-disposition:attachment;filename='.$filename);
    header('content-length:'.filesize($file));
    readfile($file);
    exit;
    
}
/**
 * 获取字符串首字母
 * @param $name 字符串
 */
function zz_first_char($str)
{
    //$char为获取字符串首个字符
    $char=mb_substr($str, 0, 1,'utf8');
    //字母直接大写返回
    if(preg_match('/[a-zA-Z]/', $char)){
        return strtoupper($char);
    }
    
    //ord获取ASSIC码值
    
    $fchar = ord($char);
    //为了兼容gb2312和utf8
    $s1 = iconv("UTF-8","gb2312", $char);
    $s2 = iconv("gb2312","UTF-8", $s1);
    
    //如果是utf8编码，则$s2=char,是gb2312则s1=char
    if($s2 == $char){$s = $s1;}else{$s = $char;}
    
    $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
    //('A', 45217, 45252),gb2312编码以拼音A开头的汉字编码为45217---45252
    
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return "#";
}

/**
 * 文件日志
 * @param $content 要写入的内容
 * @param string $file 日志文件,在web 入口目录
 */
function zz_log($content, $file = "log.txt")
{
    file_put_contents('log/'.$file, date('Y-m-d H:i:s').' '.$content."\r\n",FILE_APPEND);
}
/**
 * 数字补0
 * @param $num 传入的数字
 * @param $limit 补足的位数
 */
function zz_num($num, $limit=2)
{
    $len=$limit-strlen($num);
    if($len>0){ 
        for($i=0;$i<=$len;$i++){
            $num='0'.$num;
        }
    }
    return $num;
}
/**
 * 根据选择的条件拼接搜索
 * @param $num 传入的数字
 * @param $limit 补足的位数
 */
function zz_search($type,$name)
{ 
    switch ($type){
        case 1:
            return ['eq',$name]; 
        case 2:
            return ['like',$name.'%']; 
        case 3:
            return ['like','%'.$name];  
        default:
            return ['like','%'.$name.'%']; 
    }
}
/**
 * 根据查询时间
 * @param array $types 查询参数
 * @param array $search_types 查询类型
 * @param array &$data 查询数据
 * @param array &$where 查询条件
 * @param array 查询参数 $param
 *  name_type1=type1 查询字段类型的name
 *  name_type2=type2 查询方式的name
 *  name_name=name 查询值的name 
 *  alias 表别名的追加
 * @return array 返回data和where
 */ 
function zz_search_param($types,$search_types,&$data,&$where,$param=[]){
    //$name_type1='type1',$name_type2='type2',$name_name='name'
    // 查询字段类型的name
    $name_type1=(empty($param['name_type1']))?'type1':$param['name_type1'];
    //查询方式的name
    $name_type2=(empty($param['name_type2']))?'type2':$param['name_type2'];
    //关键字的name
    $name_name=(empty($param['name_name']))?'name':$param['name_name'];
    
    //表别名追加
    $alias=(empty($param['alias']))?'':$param['alias'];
    
    //选择查询字段
    if(empty($data[$name_type1])){
        $data[$name_type1]=key($types);
    }
    //搜索类型  
    if(empty($data[$name_type2])){
        $data[$name_type2]=key($search_types);
    }
    if(!isset($data[$name_name]) || $data[$name_name]==''){
        $data[$name_name]='';
    }else{
        switch ($data[$name_type2]){
            case 1:
                $res=['eq',$data[$name_name]];
                break;
            case 2:
                $res=['like',$data[$name_name].'%'];
                break;
            case 3:
                $res=['like','%'.$data[$name_name]];
                break;
            default:
                $res=['like','%'.$data[$name_name].'%'];
                break;
        }
        $where[$alias.($types[$data[$name_type1]][0])]=$res;
    }
    
    return 1;
}
 
/**
 * 根据查询时间
 * @param $times 时间查询
 * @param $data 查询数据
 * @param $where 查询条件 
 * @param array $param 默认字段，
 * 包括day1=31 开始时间默认提前天数，
 * day2 =0结束时间默认提前天数，
 * name_time1=datetime1 开始时间的name，
 * name_time2 =datetime2结束时间的name,
 * name_time=time时间类型选择的name,
 * alias表别名追加
 * @return string|array 错误返回说明|正常返回data和where
 */
function zz_search_time($times,&$data,&$where,$param=[])
{
    //时间查询类型的name
    $name_time=(empty($param['name_time']))?'time':$param['name_time'];
   //起始时间的name
    $name_time1=(empty($param['name_time1']))?'datetime1':$param['name_time1'];
    //结束时间的name
    $name_time2=(empty($param['name_time2']))?'datetime2':$param['name_time2'];
    //默认显示的起始时间天数
    $day1=(empty($param['day1']))?0:$param['day1'];
    //默认显示的结束时间天数
    $day2=(empty($param['day2']))?0:$param['day2'];
    //表别名追加
    $alias=(empty($param['alias']))?'':$param['alias'];
    
    if(empty($data[$name_time])){
        $data[$name_time]=key($times);
        if(empty($day1)){
            $data[$name_time1]='';
            $data[$name_time2]='';
        }else{
            $time1=time()-24*3600*$day1;
            $time2=time()-24*3600*$day2;
            $data[$name_time1]=date('Y-m-d H:i',$time1);
            $data[$name_time2]=date('Y-m-d H:i',$time2);
            $where[$alias.($times[$data[$name_time]][0])]=['between',[$time1,$time2]];
        } 
    }else{
        //时间处理
        if(empty($data[$name_time1])){
            $data[$name_time1]='';
            $time1=0;
            if(empty($data[$name_time2])){
                $data[$name_time2]='';
                $time2=0;
            }else{
                //只有结束时间
                $time2=strtotime($data['datetime2']);
                $where[$alias.($times[$data[$name_time]][0])]=['elt',$time2];
            }
        }else{
            //有开始时间
            $time1=strtotime($data[$name_time1]);
            if(empty($data[$name_time2])){
                $data[$name_time2]='';
                $where[$alias.($times[$data[$name_time]][0])]=['egt',$time1];
            }else{
                //有结束时间有开始时间between
                $time2=strtotime($data[$name_time2]);
                if($time2<=$time1){
                    return ('结束时间必须大于起始时间');
                }
                $where[$alias.($times[$data[$name_time]][0])]=['between',[$time1,$time2]];
            }
        }
    }
    return 1;
    
}
/**
 * 根据目录删除文件和目录,
 * @param $dir 传入的目录
 * @param $rate 目录深度，暂时1级
 */
 function zz_dirdel($dir,$rate=1){
     return 1;
     $files=scandir($dir,1); 
     foreach($files as $v){
         if($v[0]=='.'){
             break;
         }
         if(is_file($dir.$v)){
             unlink($dir.$v);
         }
     }
     //报错？没权限?
     unlink($dir);
     return 1; 
}
/**
 * 清除不规范分隔符输入
 * @param $dsc 输入内容
 * @param $delimiter 分隔符
 */
function zz_delimiter($dsc,$delimiter=','){
    //清除不规范输入导致的空格
    $tmp=explode($delimiter, $dsc);
    foreach($tmp as $k=>$v){ 
        $tmp[$k]=trim($v);
        if($tmp[$k]===''){
            unset($tmp[$k]);
        }
    }
    
    return implode($delimiter, $tmp);
}
/**
 * 判断密码输入
 * @param $uid 用户id
 * @param string $psw 密码
 */
function zz_psw($uid,$psw){
    $psw_count=config('psw_count');
    $m_user=Db::name('user');
    $user=$m_user->where('id',$uid)->find();
    if($user['user_pass']!=session('user.user_pass')){
        session('user',null);
        return [0,'密码已修改，请重新登录',url('user/login/login')];
    }
    //登录失败6次锁定
    if($user['psw_fail']>=$psw_count){
        return [0,'密码错误已达'.$psw_count.'次，请重新登录',url('user/login/login')];
    }
    
    if(cmf_compare_password($psw, $user['user_pass'])){
        $m_user->where('id',$uid)->update(['psw_fail'=>0]);
        return [1];
    }else{
        $m_user->where('id',$uid)->setInc('psw_fail');
        
        return [0,'密码错误'.($user['psw_fail']+1).',连续'.$psw_count.'次将退出登录!',''];
    }
    
}
/**
 * curl函数封装
 * @param $url 网址
 * @param $data 数据
 */
function zz_curl($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


/**
 *过滤HTML得到纯文本
 * @param $list 列表
 * @param $len 截取文本长度
 */
function zz_get_contents($list,$len=100,$charset='utf-8'){
    //过滤富文本
    $tmp=[];
    foreach ($list as $k=>$v){
        
        $content_01 = $v["content"];//从数据库获取富文本content
        $content_02 = htmlspecialchars_decode($content_01); //把一些预定义的 HTML 实体转换为字符
        $content_03 = str_replace("&nbsp;","",$content_02);//将空格替换成空
        $contents = strip_tags($content_03);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        $con = mb_substr($contents, 0, $len,"utf-8");//返回字符串中的前100字符串长度的字符
        $v['content']=$con.'...';
        $tmp[]=$v;
    }
    return $tmp;
}
/**
 *过滤HTML得到纯文本
 * @param $content 富文本
 * @param $len 截取文本长度
 */
function zz_get_content($content,$len=100,$charset='utf-8'){
    //过滤富文本 
   
    $content= htmlspecialchars_decode($content); //把一些预定义的 HTML 实体转换为字符
    $content= str_replace("&nbsp;","",$content);//将空格替换成空
    $content= strip_tags($content);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
    $content= mb_substr($content, 0, $len,$charset);//返回字符串中的前100字符串长度的字符
    
    return $content;
}
 
/**
 *制作缩略图
 * @param $pic 原图片
 * @param $pic_new 新图片名
 */
function zz_set_image($pic,$pic_new,$width,$height,$thump=6){
    /* 缩略图相关常量定义 */
    //     const THUMB_SCALING   = 1; //常量，标识缩略图等比例缩放类型
    //     const THUMB_FILLED    = 2; //常量，标识缩略图缩放后填充类型
    //     const THUMB_CENTER    = 3; //常量，标识缩略图居中裁剪类型
    //     const THUMB_NORTHWEST = 4; //常量，标识缩略图左上角裁剪类型
    //     const THUMB_SOUTHEAST = 5; //常量，标识缩略图右下角裁剪类型
    //     const THUMB_FIXED     = 6; //常量，标识缩略图固定尺寸缩放类型
    //         $water=INDEXIMG.'water.png';//水印图片
    //         $image->thumb(800, 800,1)->water($water,1,50)->save($imgSrc);//生成缩略图、删除原图以及添加水印
    // 1; //常量，标识缩略图等比例缩放类型
    //         6; //常量，标识缩略图固定尺寸缩放类型
    $path='upload/';
    //判断文件来源，已上传和未上传
    $imgSrc=(is_file($pic))?$pic:($path.$pic);
    
    $imgSrc1=$path.$pic_new;
    if(is_file($imgSrc)){
        $image = \think\Image::open($imgSrc);
        $size=$image->size();
        if($size!=[$width,$height] || !is_file($imgSrc1)){
            $image->thumb($width, $height,$thump)->save($imgSrc1);
        }
    }
    return $pic_new;
}
 
/**
 * 文件和图片处理
 * @param string $pic原图片
 * @param string $pathid保存路径
 * @param array $size 图片制作大小
 * @param string $path 文件根目录
 * @param number $thump 图片大小制作类型
 * @param number $is_again 是否一定要重新制作，1为一定重新制图
 * @param number $is_unlink 是否删除原图,1删除，0不删除
 * @return string 
 */
function zz_set_file($pic,$pathid,$size=[],$path='upload/',$thump=6,$is_again=0,$is_unlink=1){
    if(empty($pic)){
       return '';
    }
    //没有目录创建目录
    if(!is_dir($path.$pathid)){
        mkdir($path.$pathid);
    }
    //先比较是否需要额外保存,非指定位置的要复制粘贴
    if($is_again==0 && strpos($pic, $pathid)===0){
        return $pic;
    }
    //不是文件则返回空
    if (!is_file($pic)){
        $pic=$path.$pic;
        if (!is_file($pic)){
            return '';
        }
    } 
     
    
    //获取后缀名,复制文件
    $ext=substr($pic, strrpos($pic,'.'));
    $new_file=$pathid.date('Ymd-His').$ext;
    $result =copy($pic, $path.$new_file);
    if ($result == false)
    {
        return '';
    } 
    //删除原图片
    if($is_unlink){
        unlink($pic);
    }
   
    if(!empty($size['width']) && !empty($size['height'])){
        $image = \think\Image::open($path.$new_file);
        $image->thumb($size['width'], $size['height'],$thump)->save($path.$new_file); 
    }
    return $new_file; 
}
/**
 * 文件和图片处理,增加了自定义name
 * @param string $pic原图片
 * @param string $pathid保存路径
 * @param array $size 图片制作大小
 * @param string $path 文件根目录
 * @param number $thump 图片大小制作类型
 * @return string
 */
function zz_set_file_name($pic,$pathid,$size=[],$name='',$path='upload/',$thump=6){
    if(empty($pic)){
        return '';
    }
    //没有目录创建目录
    if(!is_dir($path.$pathid)){
        mkdir($path.$pathid);
    }
    //不是文件则返回空
    if (!is_file($path.$pic))
    {
        return '';
    }
    
    //先比较是否需要额外保存,非指定位置的要复制粘贴
    if(strpos($pic, $pathid)===0){
        return $pic;
    }
    
    //获取后缀名,复制文件
    $ext=substr($pic, strrpos($pic,'.'));
    $new_file=$pathid.date('Ymd-His').$name.$ext;
    $result =copy($path.$pic, $path.$new_file);
    if ($result == false)
    {
        return '';
    }
    //删除原图片
    unlink($path.$pic);
    if(!empty($size['width']) && !empty($size['height'])){
        $image = \think\Image::open($path.$new_file);
        $image->thumb($size['width'], $size['height'],$thump)->save($path.$new_file);
    }
    return $new_file;
}
 
/** 
 * 为网址补加http://
 * @param $link   网址
 */
function zz_link($link){
    //处理网址，补加http://
    $exp='/^(http|ftp|https):\/\//';
    if(preg_match($exp, $link)==0){
        $link='http://'.$link;
    }
    return $link;
}
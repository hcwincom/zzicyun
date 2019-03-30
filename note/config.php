<?php	return array (
  'zzsite' => 
  array (
    'name' => '器械商城',
    'keywords' => '器械商城',
    'desc' => '器械商城',
    'admin' => 'adminadmin',
  ),
  'zztarget' => 
  array (
    'list' => '_self',
    'edit' => '_self',
    'other' => '_blank',
  ),
  'zzajax' => 'bb',
     
    'pic_goods_cate' => [  
        'width'=>17,
        'height'=>17,
        'logo'=>0
    ],
    'pic_goods_brand' => [
        'width'=>150,
        'height'=>40,
        'logo'=>0
    ],
    'pic_goods' => [
        'width'=>245,
        'height'=>268,
        'logo'=>0
    ],
    'pic_icon'=>[
        'width'=>60,
        'height'=>43
    ],
    'pic_logo'=>[
        'width'=>214,
        'height'=>144
    ],
    'pic_avatar'=>[
        'width'=>60,
        'height'=>43
    ],
    'pic_gift'=>[
        'width'=>213,
        'height'=>323
    ],
    
    'shop_types'=>[
        1=>'自营',
        2=>'代售', 
        3=>'原厂专营',
        4=>'实力分销商', 
        5=>'海外代购', 
    ],
    'pdf_cates'=>[1=>'数据手册',2=>'AD封装库文件',3=>'PADS封装库文件'],
    'store_sures'=>[2=>'无需确认',1=>'需要确认'],
    'is_rohs'=>[1=>'符合标准',2=>'不符合'],
    'user_rates'=>[1=>'铜牌会员',2=>'银牌会员',3=>'金牌会员',4=>'钻石会员'], 
    'user_cates'=>[1=>'企业单位',2=>'个体经营',3=>'事业单位或社会团体'],
    'money_type'=>[1=>'人民币',2=>'美元'],
     
    /*下面是不用多语言的配置  */
    'coupon_status_use'=>[1=>'未使用',2=>'已使用'],
    'coupon_status_time'=>[1=>'未到时间',2=>'可以使用',3=>'已过期'],
    'coupon_type'=>[1=>'网站赠送',2=>'用户领取',3=>'注册赠送',4=>'推荐会员'], 
    'pay_type1'=>[1=>'在线支付',2=>'货到付款'],
    'pay_type2'=>[1=>'支付宝',2=>'微信支付',3=>'网银',4=>'银行转账',5=>'授信额度'],
    'pay_type3'=>[1=>'全额付款',2=>'预付30%',3=>'预付50%'],
    'invoice_status'=>[1=>'不开票',2=>'未开票',3=>'已开票'],  
    'invoice_type'=>[1=>'增值税发票',2=>'普通发票（公司）',3=>'普通发票（个人）'],
    'freight_type'=>[1=>'大陆快递',2=>'香港快递'],
    'score_type'=>[1=>'不限日期',2=>'每天'],
   
  'info_status' => 
  array (
    1 => '待审核',
    2 => '正常',
    3 => '不通过',
    4 => '禁用',
  ),
  'review_status' => 
  array (
    1 => '待审核',
    2 => '通过',
    3 => '不通过',
  ),
  'msg_status' => 
  array (
    1 => '未接收',
    2 => '未读',
    3 => '已读',
  ),
  'msg_types' => 
  array (
    'msg' => '通知',
    'notice' => '公告',
    'add' => '创建',
    'edit' => '编辑',
    'review' => '状态审核',
    'review_all' => '批量同意',
    'edit_review' => '编辑审核',
    'del' => '删除',
    'edit_del' => '编辑记录删除',
  ),
  'action_types' => 
  array (
    'add' => '创建',
    'edit' => '编辑',
    'review' => '状态审核',
    'review_all' => '批量同意',
    'edit_review' => '编辑审核',
    'del' => '删除',
    'edit_del' => '编辑记录删除',
  ),
  'tables' => 
  array (
    'system' => '系统任务',
    'order' => '订单',
    
    'orderback' => '售后',
    
    'store_in' => '入库',
    'store_out' => '出库',
    'notice'=>'页面提示',
    'coupon'=>'优惠券',
    'user_industry'=>'用户行业',
    
    'goods' => '产品',
    'goods_cate' => '产品分类',
    'goods_brand' => '品牌',
    'goods_param' => '技术参数',
    'goods_template' => '参数模板',
    'goods_time' => '产品发货时间',
      'gift' => '礼品',
      'gift_cate' => '礼品分类',
    'bank' => '转账银行', 
    'express' => '快递种类',
    'area' => '地区',
    'area_custom' => '配送区域',
    'freight' => '配送公司',
      
    'shop' => '供应商',
    'paytype' => '支付方式',
     
    'msg' => '消息',
    
     'department'=>'部门', 
      'score_rule'=>'积分规则',
      'station'=>'自提点',
  ),
  'search_types' => 
  array (
    4 => '模糊搜索',
    1 => '精准搜索',
    2 => '头部搜索',
    3 => '尾部搜索',
  ),
  'time1_search' => 
  array (
    'atime' => '创建时间',
    'rtime' => '审核时间',
    'time' => '更新时间',
  ),
  'pro_time_search' => 
  array (
    1 => 
    array (
      0 => 'atime',
      1 => '创建时间',
    ),
    2 => 
    array (
      0 => 'rtime',
      1 => '审核时间',
    ),
    3 => 
    array (
      0 => 'time',
      1 => '更新时间',
    ),
  ),
  'edit_time_search' => 
  array (
    1 => 
    array (
      0 => 'atime',
      1 => '编辑时间',
    ),
    2 => 
    array (
      0 => 'rtime',
      1 => '审核时间',
    ),
  ),
  'time2_search' => 
  array (
    'atime' => '编辑时间',
    'rtime' => '审核时间',
  ),
   
   
  'reg' => 
  array (
    'mobile' => 
    array (
      0 => '/(^(13\\d|15[^4\\D]|17[013678]|18\\d)\\d{8})$/',
      1 => '手机号码格式错误',
    ),
    'user_login' => 
    array (
      0 => '/^[0-9a-zA-Z]{6,20}$/',
      1 => '用户名只能是6-20位数字或英文字母组成',
    ),
  ),
  'user_search' => 
  array (
    1 => 
    array (
      0 => 'user_nickname',
      1 => '姓名',
    ),
    2 => 
    array (
      0 => 'user_login',
      1 => '用户名',
    ),
    3 => 
    array (
      0 => 'user_email',
      1 => '邮箱',
    ),
    4 => 
    array (
      0 => 'mobile',
      1 => '手机',
    ),
    5 => 
    array (
      0 => 'id',
      1 => '用户id',
    ),
  ),
  'goods_search' => 
  array (
    'name' => '全名',
    'code' => '编码',
    'code_name' => '同级名称',
    'name2' => '商城名称',
    'name3' => '打印名称',
    'id' => '产品id',
    'aid' => '创建人id',
    'rid' => '审核人id',
  ),
   
  'sn_type' => 
  array (
    1 => '无条码',
    3 => '一货一码',
    2 => '有条码',
  ),
  'is_box' => 
  array (
    2 => '无内盒',
    1 => '有内盒',
  ),
  'chars' => 
  array (
      26=>'#',
    27 => 'A',
    1 => 'B',
    2 => 'C',
    3 => 'D',
    4 => 'E',
    5 => 'F',
    6 => 'G',
    7 => 'H',
    8 => 'I',
    9 => 'J',
    10 => 'K',
    11 => 'L',
    12 => 'M',
    13 => 'N',
    14 => 'O',
    15 => 'P',
    16 => 'Q',
    17 => 'R',
    18 => 'S',
    19 => 'T',
    20 => 'U',
    21 => 'V',
    22 => 'W',
    23 => 'X',
    24 => 'Y',
    25 => 'Z',
  ),
  'invoice_type' => 
  array (
    0 => 
    array (
      0 => '不开票',
      1 => 0,
    ),
    1 => 
    array (
      0 => '增值税票',
      1 => 12,
    ),
    2 => 
    array (
      0 => '普通税票',
      1 => 6,
    ),
  ),
  'invoice_status' => 
  array (
    1 => '未开票',
    2 => '已付款',
    3 => '已开票',
    4 => '已发送',
    5 => '已收到',
  ),
  'pay_type' => 
  array (
    1 => '先付款后发货',
    2 => '货到付款', 
  ),
 
   
  'store_in_type' => 
  array (
    10 => 
    array (
      0 => '供货商入库',
      1 => '/ordersup/admin_ordersup/edit',
    ),
    20 => 
    array (
      0 => '售后入库',
      1 => '/order/admin_order/edit',
    ),
    30 => 
    array (
      0 => '库存调整',
      1 => '/order/admin_order/edit',
    ),
     
  ),
    'store_out_type' =>
    array (
        10 =>
        array (
            0 => '订单单发货',
            1 => '/ordersup/admin_ordersup/edit',
        ),
        20 =>
        array (
            0 => '售后出库',
            1 => '/order/admin_order/edit',
        ),
        30 =>
        array (
            0 => '库存调整',
            1 => '/order/admin_order/edit',
        ),
        
    ),
   
  'store_in_status' => 
  array (
    1 => '待审核',
    2 => '通过',
    3 => '不通过',  
  ),
  
  'order_time' => 
  array (
    1 => 
    array (
      0 => 'p.create_time',
      1 => '创建时间',
    ),
    2 => 
    array (
      0 => 'p.pay_time',
      1 => '支付时间',
    ),
    3 => 
    array (
      0 => 'p.send_time',
      1 => '发货时间',
    ),
    4 => 
    array (
      0 => 'p.accept_time',
      1 => '收货时间',
    ),
    5 => 
    array (
      0 => 'p.completion_time',
      1 => '完成时间',
    ),
  ),
  'ordersup_type' => 
  array (
    1 => '采购单',
    2 => '转运营',
  ),
  'pay_status' => 
  array (
    1 => '未付款',
    2 => '付款待确认',
    3 => '已确认付款',
  ),
  'freight_pay_status' => 
  array (
    1 => '未发货',
    2 => '待付款',
    3 => '已确认付款',
  ),
  'order_status' => 
  array (
    1 => '未提交',
    2 => '提交待确认',
    10 => '待付款',
    20 => '待发货',
    22 => '已准备发货',
    24 => '已发货',
    26 => '已收货',
    30 => '订单完成',
    70 => '订单关闭',
    80 => '已取消',
    81 => '已废弃',
  ),
 
);
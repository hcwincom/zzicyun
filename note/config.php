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
        'width'=>14,
        'height'=>14,
        'logo'=>0
    ],
    'pic_goods_brand' => [
        'width'=>150,
        'height'=>40,
        'logo'=>0
    ],
    'pic_goods' => [
        'width'=>270,
        'height'=>250,
        'logo'=>0
    ],
  'units' => 
  array (
    1 => 
    array (
      0 => '克',
      1 => '厘米',
      2 => '立方厘米',
    ),
    2 => 
    array (
      0 => '千克',
      1 => '米',
      2 => '立方米',
    ),
  ),
    'shop_types'=>[
        1=>'自营',
        2=>'原厂代理专营',
        3=>'实力分销商', 
    ],
    'pic_logo'=>[
        'width'=>214,
        'height'=>144
    ],
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
    'ordersup' => '采购单',
    'orderq' => '询盘',
    'orderback' => '售后',
    'store_goods' => '仓库库存',
    'store_in' => '出入库',
    'store_shelf' => '仓库货架',
    'store_box' => '仓库料位',
    'cate' => '产品分类',
    'goods' => '产品',
    'brand' => '品牌',
    'param' => '技术参数',
    'template' => '参数模板',
    'goods_fee' => '价格参数',
    'goods_fee_cate' => '价格参数分类',
    'price' => '价格模板',
    'compare' => '产品对比',
    'bank' => '转账银行',
    'shop_fee' => '店铺费用',
    'shop_fee_cate' => '店铺费用分类',
    'shop_fee_month' => '每月费用',
    'express' => '快递种类',
    'expressarea' => '配送区域',
    'freight' => '配送公司',
    'shop' => '加盟店铺',
    'paytype' => '支付方式',
    'area' => '地区',
    'store' => '仓库',
    'company' => '子公司',
    'custom' => '客户',
    'custom_cate' => '客户分类',
    'supplier' => '供货商',
    'supplier_cate' => '供货商分类',
    'msg' => '消息',
    'event' => '事件',
    'attendance_day' => '考勤',
    'attendance_rule' => '考勤规则',
    'attendance_apply' => '考勤申请',
     'department'=>'部门',
      'order_invoice'=>'发票',
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
  'file_type' => 
  array (
    1 => '极敏商城图片',
    2 => '产品实物图片',
    3 => '极敏logo图片',
    4 => '产品规格图',
    5 => '产品原理图',
    6 => '其他参考图片',
    7 => '产品说明书',
    8 => '其他文档',
  ),
  'image_type' => 
  array (
    0 => 1,
    1 => 2,
    2 => 3,
  ),
  'pic_size' => 
  array (
    1 => 
    array (
      0 => 70,
      1 => 70,
    ),
    2 => 
    array (
      0 => 330,
      1 => 330,
    ),
    3 => 
    array (
      0 => 800,
      1 => 800,
    ),
    4 => 
    array (
      0 => 150,
      1 => 150,
    ),
  ),
  'pic_brand' => 
  array (
    0 => 150,
    1 => 150,
    2 => 1,
  ),
  'cate_max' => 99,
  'prices' => 
  array (
    1 => '入库价',
    2 => '出库价',
    3 => '发货价',
    4 => '零售价',
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
      0 => '/^[0-9a-zA-Z]{2,20}$/',
      1 => '用户名只能是2-20位数字或英文字母组成',
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
  'goods_type' => 
  array (
    1 => '基本产品',
    2 => '产品组合',
    3 => '加标产品',
    4 => '加工产品',
    5 => '设备',
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
    3 => '定期结算',
  ),
  'shelf_size' => 
  array (
    'length' => 2,
    'width' => 0.5,
    'height' => 2,
    'floor' => 5,
  ),
  'store_in_type' => 
  array (
    1 => 
    array (
      0 => '供货商采购',
      1 => '/ordersup/admin_ordersup/edit',
    ),
    10 => 
    array (
      0 => '客户订单',
      1 => '/order/admin_order/edit',
    ),
    20 => 
    array (
      0 => '仓库调货',
      1 => '/order/admin_order/edit',
    ),
    21 => 
    array (
      0 => '售后入库',
      1 => '/orderback/admin_orderback/edit',
    ),
    22 => 
    array (
      0 => '售后出库',
      1 => '/order/admin_orderback/edit',
    ),
    30 => 
    array (
      0 => '添加料位入库',
      1 => '/store/admin_box/edit',
    ),
    31 => 
    array (
      0 => '手动入库',
      1 => '',
    ),
  ),
  'oid_type' => 
  array (
    1 => 
    array (
      0 => '客户订单',
      1 => '/order/admin_order/edit',
    ),
    2 => 
    array (
      0 => '供货商采购',
      1 => '/ordersup/admin_ordersup/edit',
    ),
    4 => 
    array (
      0 => '客户售后单',
      1 => '/orderback/admin_orderback/edit',
    ),
    5 => 
    array (
      0 => '采购售后单',
      1 => '/order/admin_orderback/edit',
    ),
  ),
  'company_type' => 
  array (
    1 => '线下子公司',
    2 => '淘宝店铺',
  ),
  'store_in_status' => 
  array (
    1 => '待审核',
    2 => '通过',
    3 => '不通过',
    4 => '未提交',
    5 => '废弃',
  ),
  'order_type' => 
  array (
    1 => '线下订单',
    2 => '商城订单',
    3 => '淘宝订单',
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
  'ordersup_status' => 
  array (
    1 => '未提交',
    2 => '提交待确认',
    10 => '待付款',
    20 => '待发货',
    22 => '已发货',
    24 => '已准备收货',
    26 => '已收货',
    30 => '订单完成',
    70 => '订单关闭',
    80 => '已取消',
    81 => '已废弃',
  ),
   
);
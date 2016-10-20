<?php
return array(
    /* 系统名称及版本 */
    'SYS_NAME'    => '[SYS_NAME]',
    'SYS_VERSION' => '[SYS_VERSION]',
    
    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '[DB_HOST]', // 服务器地址
    'DB_NAME'   => '[DB_NAME]', // 数据库名
    'DB_USER'   => '[DB_USER]', // 用户名
    'DB_PWD'    => '[DB_PWD]',  // 密码
    'DB_PORT'   => '[DB_PORT]', // 端口
    'DB_PREFIX' => '', // 数据库表前缀,因与manyuser共用数据库，请留空
   
     /* 模块设置 */
     'DEFAULT_MODULE'       => 'Home',
     'MODULE_DENY_LIST'     => array('Common','Admin','Install'),
     'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    
    /* View基本文件路径 */
     'TMPL_PARSE_STRING'=>array(
        '__STATIC__' => __ROOT__ . '/Public/static/',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css/',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js/',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/img/',
    ),
    
    /* 替换系统默认显示模板 */
    'TMPL_ACTION_ERROR'     =>  'Public:jump', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  'Public:jump', // 默认成功跳转对应的模板文件
    
    'ERROR_PAGE'            =>  __ROOT__ .'/404.html', // 错误定向页面  
    'URL_404_REDIRECT'      =>  __ROOT__ .'/404.html', // 404 跳转页面 部署模式有效
    
    /* 系统数据加密盐值设置，主要是为了兼容ss-panel */
    'DATA_AUTH_KEY' => '[AUTH_KEY]', //数据加密KEY，安装后切勿改动，并做备份记录
    'PWD_MODE'      => 3 ,//1-MD5;2-sha256;默认-yaopanel;
    
    /* 主要是实现后台动态配置 */
    'LOAD_EXT_CONFIG' => 'setting',//扩展配置文件
);
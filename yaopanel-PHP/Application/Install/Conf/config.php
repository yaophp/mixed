<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(
    
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式 使用兼容模式安装
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    'ORIGINAL_TABLE_PREFIX' => '', //默认表前缀
    
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
);
<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');


//系统是否已经安装
if (!is_file('./Data/install.lock')) {
    header('Location: ./install.php');
    exit;
}

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
//define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','./Application/');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

include './ThinkPHP/ThinkPHP.php';
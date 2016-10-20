<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

//版本要求
if(version_compare(PHP_VERSION, '5.3.0','<')){exit('PHP版本要5.3以上！');}

//应用目录
define('APP_PATH', './Application/');

//缓存目录 此目录必须可写，建议移动到非WEB目录
define('RUNTIME_PATH','./Runtime/');

//调试模式，安装时，如果有错误可以抛出
define ( 'APP_DEBUG', true );

//绑定到Install模块
define ( 'BIND_MODULE','Install');

//版本号
define('SYS_NAME','Yaopanel');
define('SYS_VERSION','2.0.18');

require './ThinkPHP/ThinkPHP.php';



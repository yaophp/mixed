<?php
return array(

    /* session 和 cookie */
    'SESSION_PREFIX' => 'admin',
    'COOKIE_PREFIX'  => 'admin_',
    
    /* 图片上传相关配置 */
    'UPLOAD_PICTURE_UMEDITOR' => array(
		'mimes'    => '', //允许上传的文件MiMe类型
		'maxSize'  => 1024*1024, //上传的文件大小限制1M (0-不做限制)
		'exts'     => 'jpg,gif,png', //允许上传的文件后缀
		'autoSub'  => FALSE, //自动子目录保存文件
		'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'rootPath' => './Data/upload/', //保存根路径
		'savePath' => 'umeditor/', //保存路径
		'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveExt'  => '', //文件保存后缀，空则使用原后缀
		'replace'  => false, //存在同名是否覆盖
		'hash'     => true, //是否生成hash编码
		'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

);
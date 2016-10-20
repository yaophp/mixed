<?php

/*
 * Install 的 function
 */

//版本比较函数,以便确定是否需要升级
function check_update(){
    $ver_install = intval(str_replace('.', '', C('SYS_VERSION')));
    $ver_now     = intval(str_replace('.', '', SYS_VERSION));
    return $ver_install < $ver_now ? TRUE : FALSE;
}


// 检测环境是否支持可写
define('IS_WRITE', APP_MODE !== 'sae');

/*
 * 检查安装环境
 */

function check_env() {
    $items = array(
        'os' => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php' => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'success'),
        'upload' => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd' => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'disk' => array('磁盘空间', '50M', '100M+', '未知', 'success'),
    );
    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session('error', true);
    }
    //上传附件检测
    if (@ini_get('file_uploads')) {
        $items['upload'][3] = @ini_get('upload_max_filesize');
    }
    //GD库检查
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', TRUE);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(INSTALL_APP_PATH) / (1024 * 1024)) . 'M';
    }

    return $items;
}

/*
 * 文件目录读写检测
 */

function check_dirfile() {
    $items = array(
//        array('dir', '可写', 'success', './Uploads/Download'),
//        array('dir', '可写', 'success', './Uploads/Picture'),
//        array('dir', '可写', 'success', './Uploads/Editor'),
        array('dir', '可写', 'success', './Runtime'),
        array('dir', '可写', 'success', './Data'),
//        array('dir', '可写', 'success', './Application/User/Conf'),
        array('file', '可写', 'success', './Application/Common/Conf'),
    );

    foreach ($items as & $val) {
        $item = INSTALL_APP_PATH . $val[3];
        if ($val[0] == 'dir') {
            if (!is_writable($item)) {
                if (is_dir($item)) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', TRUE);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($item)) {
                if (!is_writable($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }
    unset($val);
    return $items;
}

/*
 * 检查函数是否可用
 */

function check_func() {
    $items = array(
        array('pdo', '支持', 'success', '类'),
        array('pdo_mysql', '支持', 'success', '模块'),
        array('file_get_contents', '支持', 'success', '函数'),
        array('mb_strlen', '支持', 'success', '函数'),
    );
    foreach ($items as & $val) {
        if (( $val[3] == '类' && !class_exists($val[0]) ) || ( $val[3] == '模块' && !extension_loaded($val[0]) ) || ( $val[3] == '函数' && !function_exists($val[0]) )) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', TRUE);
        }
    }
    unset($val);
    return $items;
}

/*
 * 将配置写入文件
 */
//function write_config($config, $auth) {
//    if (is_array($config)) {
//        $conf = file_get_contents(COMMON_PATH . 'config.php');
//        foreach ($config as $name => $value) {
//            $conf = str_replace("[{$name}]", $value, $conf);
//        }
//        $conf = str_replace("[AUTH_KEY]", $auth, $conf);
//        //将配置写入文件
//        if (!IS_WRITE) {
//            return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>' . realpath(APP_PATH) . '/Common/Conf/config.php</p>
//            <textarea name="" style="width:650px;height:185px">' . $conf . '</textarea>' ;
//        } else {
//            if (file_put_contents(COMMON_PATH . 'config.php', $conf)) {
//                show_msg('配置文件写入成功');
//            } else {
//                show_msg('配置文件写入失败！', 'error');
//                session('error', true);
//            }
//            return '';
//        }
//    }
//}
function write_config($config, $auth) {
    if (is_array($config)) {
        show_msg('开始创建配置文件...');
        $conf = file_get_contents(MODULE_PATH . 'Data/conf.tpl');
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }
        $conf = str_replace(array("[AUTH_KEY]", "[SYS_NAME]", "[SYS_VERSION]"), 
                            array($auth, SYS_NAME, SYS_VERSION),
                            $conf);

        //将配置写入文件
        if (!IS_WRITE) {
            return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>' . realpath(APP_PATH) . '/Common/Conf/config.php</p>
            <textarea name="" style="width:650px;height:185px">' . $conf . '</textarea>' ;
        } else {
            if (file_put_contents(APP_PATH . 'Common/Conf/config.php', $conf)) {
                show_msg('配置文件写入成功！','success');
            } else {
                show_msg('配置文件写入失败！', 'error');
                session('error', true);
            }
            return '';
        }
    }
}


/*
 * 创建数据表
 */
function create_tables($db , $prefix=''){
    //读取sql文件
    $sql = file_get_contents(MODULE_PATH . 'Data/install.sql');
    $sql = str_replace('\r', '\n', $sql);
    $sql = explode(';\n', $sql);
    //替换表前缀
    if($prefix != ''){
      $orginal = C('ORIGINAL_TABLE_PREFIX');
      $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);  
    }
    //开始安装
    show_msg('开始安装数据库...');
    foreach($sql as $value){
        $value = trim($value);
        if(empty($value)) continue;
        if(stripos($value, 'create table') !== FALSE){
//        if(substr($value, 0,12) == 'CREATE TABLE'){
            $name = preg_replace("/`CREATE TABLE `(\w+)` .*/s", '\\1', $value);
            $msg  = "创建数据表{$name}";
            if(false !== $db->execute($value)){
                show_msg($msg . '...成功！');
            }else{
                show_msg($msg . '...失败！' ,'error');
                session('error',TRUE);
            }
        }else{
            $db->execute($value);
        }
    }
    show_msg('安装数据库完成！','success');
}

/*
 * 创建创始人账号
 */
function register_administrator($db, $prefix, $admin, $auth){
    show_msg('开始创建创始人账号...');
    $sql = "INSERT INTO `[PREFIX]user` VALUES " .
           "('1', '[EMAIL]', '[PASS]', '[PASSWD]', '','','','10736344498176','50000','1','1','7','0', '0', [TIME_CREATE], '0');";
    $sql = str_replace(
        array('[PREFIX]', '[EMAIL]', '[PASS]', '[PASSWD]','[TIME_CREATE]'),
        array($prefix, $admin['email'], pwd_hash($admin['password'] , $auth), $admin['password'],time()),
        $sql);
    //执行sql
    $db->execute($sql);
    
    $sql = "INSERT INTO `[PREFIX]user_admin` VALUES ".
           "('1','1');";
    $sql = str_replace('[PREFIX]', $prefix, $sql);
    $db->execute($sql);
    
    show_msg('创始人帐号注册完成！','success');
}

/*
 * 更新数据表
 */
function update_tables($db, $prefix = ''){
    //读取SQL文件
    $sql = file_get_contents(MODULE_PATH . 'Data/update.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    if($prefix != ''){
        $sql = str_replace(" `", " `{$prefix}", $sql);
    }

    //开始安装
    show_msg('开始升级数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if(empty($value)) continue;
        if(substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg  = "创建数据表{$name}";
            if(false !== $db->execute($value)){
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            if(substr($value, 0, 8) == 'UPDATE `') {
                $name = preg_replace("/^UPDATE `(\w+)` .*/s", "\\1", $value);
                $msg  = "更新数据表{$name}";
            } else if(substr($value, 0, 11) == 'ALTER TABLE'){
                $name = preg_replace("/^ALTER TABLE `(\w+)` .*/s", "\\1", $value);
                $msg  = "修改数据表{$name}";
            } else if(substr($value, 0, 11) == 'INSERT INTO'){
                $name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
                $msg  = "写入数据表{$name}";
            }
            if(($db->execute($value)) !== false){
                show_msg($msg . '...成功');
            } else{
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        }
    }
}


/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = ''){
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
    if($class === 'error'){die;}
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function user_md5($str, $key = ''){
    return '' === $str ? '' : md5(sha1($str) . $key);
}
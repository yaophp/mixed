<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
/*
 * 用户是否输入优惠码
 */
function is_use_coupon(){
    $code = session('code_coupon');
    if(C('SWITCH_COUPON_USE') && preg_match('/^[A-Za-z0-9]{10}$/', $code)){
        return $code;
    }else{
        return FALSE;
    }
}

/*
 * 输出用户名，用于前端
 */
function user_name(){
    $user = session('user_name');
    if(!$user){
        $user = strstr(session('user_auth.email'), '@', TRUE);
        session('user_name',$user);
    }
    echo $user;
}

//php生成GUID
function getGuid($length = 10) {
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
//    $hyphen = chr(45); // "-"
    if($length > 0 && $length<32 ){
        $uuid =  $length == 10 ? substr($charid, 8, 5) . substr($charid, 16, 5) : substr($charid, 0,$length);
    }else{
        $uuid = $charid;
    }
    return $uuid;
}

//站点关闭维护
function site_close() {
    if (C('WEB_SITE_CLOSE') == FALSE) {
        header("HTTP/1.1 503 Service Temporarily Unavailable");
        header("Status: 503 Service Temporarily Unavailable");
        header("Retry-After:" . 60 * 60 * 12);
        exit('<!doctype html><html><meta http-equiv="content-type" content="text/html;charset=utf-8"><title>站点升级维护中</title><body>为了提供更优质的服务，站点正在升级维护中，给您带来的不便请谅解。。。</body></html>');
    }
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

//* 即时输出 */
function yao_output($func , $args="" ,$time = 0){
    $time_start = time();
    set_time_limit($time);
    ob_end_flush();//关闭缓存 
    ob_implicit_flush(true);
    echo str_repeat(" ", 1024),'<br>';
    echo "Do not close this page!";
    echo "<br>";
    if(is_array($func)){
        call_user_func_array($func, $args);
    }else{
        call_user_func($func,$args);
    }
    echo "task finished";
    time_cost($time_start, time());
}

/*
 * 流量单位转换b->Kb,b->Mb,b->Gb,当反转时，Kb->b,Mb->b,Gb->b
 * @param str $type G:Gb,M:Mb,K:Kb
 * @param bool $reversion 反转，即是 Gb->b,Mb->b,Kb->b
 * @param int $round 保留几位小数
 */
function byte_to_unit($byte, $type = 'G',$reversion=false ,$round=2){
    switch (strtolower($type)){
        case 'g':
            $G = 1024*1024*1024;
            return $reversion== TRUE ? round($byte*$G) : round($byte/$G , $round);
        case 'm':
            $M = 1024*1024;
            return $reversion== TRUE ? round($byte*$M) : round($byte/$M , $round);
        default :
            $K = 1024;
            return $reversion== TRUE ? round($byte*$K):round($byte/$K , $round);
    }
}

/*
 * 返回本年/本月/本周/今天 00:00:00 的时间戳
 * @param str $date y-本年， m-本月，w-本周，其他-今天
 */
function time_begin($date='m'){
    switch (strtolower($date)){
        case 'y':
            return mktime(0,0,0,1,1,date('Y'));
        case 'm':
            return mktime(0,0,0,date('m'),1);
        case 'w':
            return mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
        default :
            return mktime(0,0,0);
    }
}

/*
 * 用户是否是管理员
 * @param int $id 用户id，超级管理员id=1
 * @return bool
 */
function is_admin($id){
    $uid = intval($id);
    if($uid == 1) {//创始人id
        return TRUE;
    }
    return M('user_admin')->where(array('uid'=>$uid))->find() ? TRUE : FALSE;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}


/*
 * 分页，统一输出格式
 * @param int $count
 * @param int $num
 */
function yao_page($count,$num = 15 ){
    $rows = intval(C('LIST_ROWS'));
    if($rows != '' && $num == 15){
        $num = $rows;
    }
    $page  = new \Think\Page($count,$num);
    $page->setConfig('theme','%LINK_PAGE% %DOWN_PAGE% ');
    $page->setConfig('next', '下一页');
    $p = $page->show();
    //处理$P为空时前端页面的问题
    if(strpos($p, '1') === FALSE ){
        $p = ' ';
//        $p = '<div><span>1</span></div>';
    }
    $limit = $page->firstRow . ',' . $page->listRows;
    return array('page'=>$p, 'limit'=>$limit);
}

/*
 * 概率事件
 * 用于生产coupon off
 */
function get_rand($proArr) { 
    $result = ''; 
    //概率数组的总概率精度 
    $proSum = array_sum($proArr); 
    //概率数组循环 
    foreach ($proArr as $key => $proCur) { 
        $randNum = mt_rand(1, $proSum);             //抽取随机数
        if ($randNum <= $proCur) { 
            $result = $key;                         //得出结果
            break; 
        } else { 
            $proSum -= $proCur;                     
        } 
    } 
    unset ($proArr); 
    return $result; 
}
/*
 * 系统密码加密
 * 向ss-panel兼容
 * @param  string $str 要加密的字符串
 * @return string 
 */
function pwd_hash($str, $salt = '') {
    if($salt == ''){
        $salt = C('DATA_AUTH_KEY');
    }
    $pwd_mode = C('PWD_MODE');
    switch ($pwd_mode) {
        case 1 :
            return md5($str);
        case 2 :
            return hash('sha256', $str . $salt);
        default:
            return think_ucenter_md5($str,$salt);
    }
}

/**
 * 系统默认非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string 
 */
function think_ucenter_md5($str, $key = ''){
        if($key == ''){ 
            $key = C('DATA_AUTH_KEY');
        }
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/*
 * 验证码生成
 * 注意：验证时$id要同一，默认99
 */
function verify_build($id = 99) {
    $conf = array(
        'fontSize' => 32, //修改字体大小(px)
        'length' => 4,
        'bg' => array(255, 255, 255),
    );
    ob_clean();
    $verify = new \Think\Verify($conf);
    $verify->entry($id);
}

/**
 * 验证码检测
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function verify_check($code, $id = 99){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key(){
	$chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
	$chars  = str_shuffle($chars);
	return substr($chars, 0, 40);
}
<?php

/*
 * +----------------------------------------------------------------------
 * | yao-[ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao <YaoPHP@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Api;

class SsoApi extends BaseApi{
    /*
     * 如果想更安全保证session可考虑添加用户的设备指纹，如header，手机串码等
     */

    /*
     * 用户是否已经登录
     * @return 0-未登录  >0登录的用户id
     */
    public function isLogin() {
        $uid = session('uid');
        if (!empty($uid)) {
            return $uid;
        }
        /* cookie方式认证 */
        $auth = cookie('user_auth');
        $sKey = cookie('user_auth_sign');
        $user = self::decode($auth);
        if(empty($auth) || empty($sKey) || empty($user)){
            return 0;
        }
        if (TRUE === self::sKey($user,$sKey)) {
            $result = self::M('user')->where(array('id' => intval($user['id'])))->field('id,id_session,time_last_login')->find();
            if ($result && $result['time_last_login'] == $user['time_last_login']) {
                if(session_id() != $result['id_session']){
                    session_unset();
                    session_destroy();
                    session_id($result['id_session']);
                    session_start();
                }
                if (empty(session('uid'))) {//服务器session已经过期
                    session_regenerate_id();
                    session('uid',$result['id']);
                    $result['id_session'] = session_id();
                    self::M('user')->save($result);
                }
                return $result['id'];
            } 
        }
        cookie('user_auth',null);
        cookie('user_auth_sign',null);
        return 0;
    }

    /*
     * 用户单点登录
     * 其他模块可以调用此接口实现登录
     */
    public function ssoLogin() {
        $data = self::ssoTalk();
        if(!$data || !isset($data['auth']) || !isset($data['sign'])){
            exit; //or 小黑屋
        }
        $user = self::decode($data['auth']); //用户资料解密
        if ($user && TRUE === self::sKey($user, $data['sign'])) {
            $result = self::M('user')->field('id_session,time_last_login,ip_last_login')->where(array('id' => intval($user['id'])))->find();
            $id_session = $result && intval($result['time_last_login']) == $user['time_last_login'] && intval($result['time_last_login']) + 30 > time() && $result['ip_last_login'] == get_client_ip() ?
                    $result['id_session'] : FALSE;
            if ($id_session) {
                if ($id_session != session_id()) {
                    session_unset();
                    session_destroy();
                }
                session_id($id_session);
                session_start();
                if($user['remember'] > 0){
                    cookie('user_auth', $data['auth'], array('expire' => $user['remember']));
                    cookie('user_auth_sign', $data['sign'],array('expire'=>$user['remember']));
                }
                return TRUE;
            }
        }
        return FALSE;
    }
    
    
    /*
     * 退出登录
     * 无需sso通信
     */
    public function ssoLogout() {
        cookie('user_auth',null);
        cookie('user_auth_sign',null);
        $uid = session('uid');
        if($uid){
            self::M('user')->where(array('id'=>intval($uid)))->setField('time_last_login', time());
            session(null);
        }
        return TRUE;
    }
    
    
    /*
     * sso通信对话
     * @param arr/str $data 要通信的数据,空-表示接收通信，非空-则发送通信
     * @param int $expire 通信超时,默认30秒
     * return 请求-加密密文 接受-解密数据/FALSE
     */
    public static function ssoTalk($data=array(),$expire=99999){
       if(!empty($data)){ //发起通信
           return self::encode(array('data'=>$data, 'skey'=>self::sKey($data)), $expire);
       } else{ //接受通信
           $data_talk = I('get.ssotalk','');
           if(empty($data_talk) || strlen((string) $data_talk) > 1024) { //接收通信数据密文最大长度
               return FALSE;
           }
           $data = self::decode($data_talk);
           if(!$data || empty($data)){
               return FALSE;
           }
           return TRUE === self::sKey($data['data'],$data['skey']) ? $data['data'] : FALSE;
       }
    }


    /*
     * 数据签名
     * @param arr $data 数据
     * @param str $sKey 数据签名
     * @return 默认返回该数据签名，如果$sKey非空，则检验是否是该数据签名
     * 请使用恒等判断是否是数据签名
     */
    public static function sKey($data, $sKey = '') {
        if (empty($data)) {
            return empty($sKey) ? '' : FALSE;
        }
        if (!is_array($data)) {
            $data = (array) $data;
        }
        $key = md5(sha1(implode(',', $data)) . SALT_SSO);
        if (empty($sKey)) {
            return $key;
        } else {
            return $key === $sKey ? TRUE : FALSE;
        }
    }

    
    /*
     * 基于onethink的加密解密函数，
     * 做了点优化，使其支持数组
     */
    public static function encode($data, $expire=0) {
        $key = md5(SALT_SSO);
        $data = base64_encode(json_encode($data));
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l)
                $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        $str = sprintf('%010d', $expire ? $expire + time() : 0);

        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    }

    public static function decode($data) {
        $key = md5(SALT_SSO);
        $data = str_replace(array('-', '_'), array('+', '/'), $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data = substr($data, 10);

        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = $str = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l)
                $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return json_decode(base64_decode($str), TRUE);
    }

    /**
     * 系统加密解密方法，来源:
     * http://www.thinkphp.cn/code/282.html
     * 安全URL编码
     * @param type $data
     * @return type
     */
    public static function t_encode($data) {
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode(json_encode($data)));
    }

    /**
     * 安全URL解码
     * @param type $string
     * @return type
     */
    public static function t_decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        ($mod4) && $data .= substr('====', $mod4);
        return json_decode(base64_decode($data),TRUE);
    }

}

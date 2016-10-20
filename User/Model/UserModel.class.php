<?php

/* 
 * +----------------------------------------------------------------------
 * | yao-[ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao <YaoPHP@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Model;

class UserModel extends BaseModel{
    
    protected static $_preg = array( //用户资料正则
        'mobile' => '/^1(3[0-9]|5[012356789]|8[0256789]|7[0678])\d{8}$/',
        'email' => '/^[A-za-z0-9_-]{2,32}@(qq\.com|163\.com|gmail\.com|hotmail\.com|126\.com|vip\.qq\.com|vip\.163\.com)$/i',
        'name' => '/^([\x{4e00}-\x{9fa5}]|[A-Za-z]){1}[\x{4e00}-\x{9fa5}A-Za-z0-9]{1,15}$/u',
        'password' => '/^[A-Za-z0-9_]{6,16}$/'
    );

    protected $_auto = array(
        array('password', 'passwordSha1', self::MODEL_BOTH, 'callback'),
        array('time_register', 'time', self::MODEL_INSERT, 'function'),
        array('ip_register', 'get_client_ip', self::MODEL_INSERT, 'function'),
    );
    
    /*
     * 初始化
     */
    protected function _initialize(){
        $this->_validate = array(
            array('mobile', self::$_preg['mobile'], '请填写正确的手机号'),
            array('mobile','', '该手机号已被注册', self::EXISTS_VALIDATE, 'unique'),
            array('email', self::$_preg['email'], '推荐使用qq、163、126、gmail、hotmail邮箱注册'), //暂时仅仅支持常见的邮箱
            array('email', '', '该邮箱已被注册', self::EXISTS_VALIDATE, 'unique'),
            array('name', self::$_preg['name'], '用户名由2-16位中文英文或数字组成且不以数字开头'),
            array('name', '', '该用户名已被注册', self::EXISTS_VALIDATE, 'unique'),
            array('password', self::$_preg['password'], '密码由6-16个数字、大小写字母、下划线组成'),
        );
    }

    /*
     *判断用户登录
     */
    public function exists($user, $pass) {
        if (preg_match(self::$_preg['name'], $user)) {
            $where['name'] = $user;
        } elseif (preg_match(self::$_preg['email'], $user)) {
            $where['email'] = $user;
        } elseif (preg_match(self::$_preg['mobile'], $user)) {
            $where['mobile'] = $user;
        } else {
            $this->error = '请输入正确的手机/邮箱/用户名';
            return FALSE;
        }
        if(!preg_match(self::$_preg['password'], $pass)){
            $this->error = '密码由6-16个数字、大小写字母、下划线组成';
            return FALSE;
        }
        $tb_user = $this->where($where)->find();
        if ($tb_user && $tb_user['password'] == $this->passwordSha1($pass)){
            return $tb_user['id']; 
        }else{
            $this->error = '帐号或密码错误'; //不论存在帐号与否
            return FALSE;
        }
    }
    
    /*
     * 更新登录情况，并做好sso准备
     * @param int $uid  用户id
     * @param bool $remember 记住登录单位秒
     */
    public function updateLogin($uid, $remember = 0){
        $result = $data = array(
            'id' => $uid,
            'time_last_login' => NOW_TIME
        );
        $data['id_session'] = session_id();
        $data['ip_last_login'] = get_client_ip();
        
        if($this->save($data)){
            $result['remember'] = $remember;
            $auth = \User\Api\SsoApi::encode($result, $remember==0 ? 30 : $remember);
            $sign = \User\Api\SsoApi::sKey($result);
            if($remember > 0){
                cookie('user_auth', $auth, array('expire' => $remember));
                cookie('user_auth_sign', $sign,array('expire' => $remember));
            }
            session('uid',$result['id']);
            return \User\Api\SsoApi::ssoTalk(array('auth'=>$auth, 'sign'=>$sign)); //返回sso通信密文
        }else{
            return FALSE;
        }
    }
    

    /*
     * 密码加密方式
     */
    protected function passwordSha1($pass){
	return '' === $pass ? '' : md5(sha1($pass) . SALT_PSW);
    }
    
}

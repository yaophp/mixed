<?php

/* 
 * +----------------------------------------------------------------------
 * | yao-[ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao <YaoPHP@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Api;
defined('MODULE_USER_PATH') or define('MODULE_USER_PATH', dirname(dirname(__FILE__)));
$__USER__CONFIG = array_change_key_case(load_config(MODULE_USER_PATH . '/Conf/config.php'),CASE_UPPER);
defined('SALT_SSO') or define('SALT_SSO', $__USER__CONFIG['SALT_SSO']);
require_cache(MODULE_USER_PATH . '/Common/function.php');

abstract class BaseApi{
    protected static $_user_config = array();
    
    protected static function _include(){
        if(empty(self::$_user_config)){
            self::$_user_config = array_change_key_case(load_config(MODULE_USER_PATH . '/Conf/config.php'),CASE_UPPER);
            defined('SALT_SSO') or define('SALT_SSO', self::$_user_config['SALT_SSO']);
        }
        
    }

    public function __construct() {
        self::_include();
        $this->_initialize();
    }
    
    /*
     * 安全M方法
     * 对User的数据库进行操作时，使用这个函数是安全的，
     */
    public static function M($user = 'user'){
        self::_include();
        return M($user, isset(self::$_user_config['DB_PREFIX']) ? self::$_user_config['DB_PREFIX'] : '',  self::$_user_config);
    }
    
    /*
     * 安全C方法，只读！！！
     * 对User的这Module的config的读取操作，该函数是安全的
     */
    public static function C($name=''){
        self::_include();
        if($name == ''){
            return self::$_user_config;
        }
        if(!strpos($name, '.')){
            $name = strtoupper($name);
            return isset(self::$_user_config[$name]) ? self::$_user_config[$name] : '';
        }
        $name = explode('.', $name);
        $name[0] = strtoupper($name[0]);
        return isset($self::$_user_config[$name[0]][$name[1]]) ? $self::$_user_config[$name[0]][$name[1]] : '';
    }
    
    
    protected function _initialize(){}
}
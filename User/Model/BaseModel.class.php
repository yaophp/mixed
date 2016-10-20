<?php

/* 
 * +----------------------------------------------------------------------
 * | yao-[ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao <YaoPHP@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Model;
use Think\Model;

abstract class BaseModel extends Model{
    
    protected $_user_config = array();// Conf/config.php
    
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        defined('MODULE_USER_PATH') || define('MODULE_USER_PATH', dirname(dirname(__FILE__)));
        if(empty($this->_user_config)){
            $this->connection = $this->_user_config = array_change_key_case(load_config(MODULE_USER_PATH . '/Conf/config.php'), CASE_UPPER);
        }
        define('SALT_PSW', $this->_user_config['SALT_PSW']);
        parent::__construct($name, $tablePrefix, $connection);
        
    }
    
}

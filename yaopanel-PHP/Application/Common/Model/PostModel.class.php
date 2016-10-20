<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Common\Model;
class PostModel extends YaoModel{
    protected $_model_join = "__USER__";
    protected $_alias = "p"; //别名
    protected $_alias_join = "u"; //别名user
    protected $_model_foreign = "uid"; //外键
    protected $_model_join_pk = "id"; //join 表主键
    
    protected $_validate = array(
        array('title','/[\S]{2,64}/','请认真输入标题！',self::EXISTS_VALIDATE), 
        array('catalog','/^[A-Za-z]{3,16}$/','请认真选择目录！',self::EXISTS_VALIDATE), 
        array('content','/[\S]{4,3200}/','请认真输入内容！',self::EXISTS_VALIDATE), 
    );


    protected $_auto = array(
        array('uid',UID),
        array('catalog','cataLog',self::MODEL_INSERT,'callback'),
        array('time_create','time',self::MODEL_INSERT,'function'),
        array('time_update','time',self::MODEL_BOTH,'function'),
    );
    
    protected function cataLog($args){//现在写死catalog只有三种
        switch ($args){
            case 'notify':
                break;
            case 'help':
                break;
            default :
                $args = 'other';
        }
        return $args ; 
    }
}

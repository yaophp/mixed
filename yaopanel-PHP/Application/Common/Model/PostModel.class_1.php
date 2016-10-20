<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Common\Model;
class PostModel extends CommonModel{

    protected $_alias = "p"; //别名
    
    protected $_validate = array(
        array('title','/[\S]{2,64}/','请认真输入标题！'), 
        array('catalog','/^[A-Za-z]{3,16}$/','请认真选择目录！'), 
        array('content','/[\S]{4,3200}/','请认真输入内容！'), 
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
    
    protected function _initialize() {
        parent::_initialize();
        $field = array(
            'index' => "$this->_alias.id,title,catalog,$this->_alias.time_create,$this->_alias.time_update,$this->_alias_u.email",
            'edit' => "id,title,catalog,content,time_update"
                );
        $this->fieldSet($field);
        $this->_field =  "$this->_alias.id,title,catalog,$this->_alias.time_create,$this->_alias.time_update,$this->_alias_u.email,$this->_alias.content";
    }

    public function getList($where, $limit, $field = '') {
        if (!empty($where[$this->getPk()])) {
            $result = $this->field($this->fieldGet())->where($where)->find();
        } else {
            $result = $this->alias($this->_alias)
                            ->field($this->_field)
                            ->join("__USER__ $this->_alias_u ON $this->_alias.uid=$this->_alias_u.id")
                            ->where($where)->limit($limit)->order("$this->_alias.id desc")->select();
        }
        return $result;
    }

}

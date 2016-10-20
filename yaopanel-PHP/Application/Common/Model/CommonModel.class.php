<?php

/*
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

/*
 * 通用模型基类
 * 优化时想到的方案
 */

namespace Common\Model;

use Think\Model;

abstract class CommonModel extends Model {

    protected $_alias = "m"; //别名
    protected $_alias_u = "u"; //join user表  别名
    protected $_alias_u2 = "u2";//left join user表  别名
    protected $_field_action = array();//可以为每个action指定字段
    protected $_field = ""; //当前action如果没有指定字段，则自动调用这里的field
    


    /*
     * 手动设置field
     */
    public function fieldSet($field){
        $field = array_change_key_case($field);
        $this->_field_action = array_change_key_case($this->_field_action);
        $action = strtolower(ACTION_NAME);
        return (is_array($field) && (isset($field[$action]) || !$field[0])) ? $this->_field_action = array_merge($this->_field_action, $field) : $this->_field = $field;
    }
    
    /*
     * 获取当前field
     * 暂时protected
     */
    protected function fieldGet(){
        $this->_field_action = array_change_key_case($this->_field_action);
        $action = strtolower(ACTION_NAME);
        return isset($this->_field_action[$action]) ? $this->_field_action[$action] : $this->_field ;
    }

    /*
     * 获取资源
     */
    public function getList($where, $limit) {
        if (!empty($where[$this->getPk()])) { //主键则返回一条
            $result = $this->where($where)->find();
        } else {
            $result = $this->alias($this->_alias)
                            ->field($this->fieldGet())
                            ->where($where)->limit($limit)->order("id desc")->select();
        }
        return $result;
    }

    /*
     * 新增资源
     * 注意create 的主键问题
     */
    public function postList($dataList = array()){
        if(FALSE != $dataList = $this->isCreate($dataList)){
            return isset($dataList[0]) ? $this->addAll($this->updateData($dataList)) : $this->add($this->updateData($dataList));
        }
        return FALSE;
    }
    
    /*
     * 更新资源
     */
    public function putList($where,$data){
        return $this->where($where)->save($this->isCreate($data));
    }
    
    /*
     * 删除资源
     */
    public function deleteList($where){
        return $this->where($where)->delete();
    }
    
    /*
     * 让子类在数据入库前还有修改数据的机会
     */
    protected function updateData($dataList){
        return $dataList;
    }
    
    /*
     * 写入操作要留意
     * 检查是否为空，为空则调用create方法
     * 并防止二次调用create方法带来的BUG
     */
    protected function isCreate($dataList){
        if(empty($dataList) || !is_array($dataList)){//防止二次调用create
            $dataList = empty($this->data) ? $this->create() : $this->data ;
        }
        return $dataList;
    }
    
}

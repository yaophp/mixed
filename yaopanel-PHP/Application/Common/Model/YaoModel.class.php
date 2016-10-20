<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

/*
 * 基础Model类 ,参考 restful 的 post-添加 ，get-获取，put-更新， delete-删除
 * 如果是关联模型，请继承扩展
 * 注意Thinkphp对于插入、更新的数据，如果field为""，则无法插入有效数据,所以要做好处理
 */

namespace Common\Model;
use Think\Model;

abstract class YaoModel extends Model{
    
    protected $_alias = ""; //别名
    protected $_model_join = ""; //inner jion
    protected $_alias_join = ""; //inner jion别名
    protected $_model_fk = ""; //外键
    protected $_model_join_pk = ""; //join 表主键


    protected $_field = array(//提取数据模型的字段
         'get'  => "",
         'post' => "",
         'put'  => "",
     ); 
    
//     protected function _initialize() {
////         if($this->_alias != ""){
////             $this->alias($this->_alias);
////         }
//     }

     /*
     * 获取数据
     */
    public function getList($where , $limit = 1 ,$field = ""){
        if($this->_alias && $this->_model_join && $this->_alias_join && $this->_model_foreign && $this->_model_join_pk){
            $this -> alias($this->_alias)
                  -> join("$this->_model_join $this->_alias_join ON $this->_alias.$this->_model_foreign=$this->_alias_join.$this->_model_join_pk");
        }
        $this->field(empty($field) ? (isset($this->_field['get']) ? $this->_field['get'] : $field ) : $field)->where($where);
        return $limit == 1 ? $this->find() : $this->limit($limit)->select();
    }
    
    /*
     * 新增数据
     * 注意避免出现$this->field("")的情况
     */
    public function postList($dataList ,$field = ""){
        !empty($field) ? $this->field($field) : ( empty($this->_field['post']) ?: $this->field($this->_field['post']));//这里要注意field 为空则无法插入有效数据
        if(FALSE != $dataList = $this->isCreate($dataList,  Model::MODEL_INSERT)){
            return isset($dataList[0]) ? $this->addAll($dataList) : $this->add($dataList);
        }
        return FALSE;
    }


    /*
     * 更新数据
     * 注意避免出现$this->field("")的情况
     */
    public function putList($where , $data ,$field = ""){
        !empty($field) ? $this->field($field) : ( empty($this->_field['post']) ?: $this->field($this->_field['post']));//这里要注意field 为空则无法插入有效数据
        return $this->where($where)->save($this->isCreate($data,  Model::MODEL_UPDATE));
    }
    
    /*
     * 写入操作要留意
     * 检查是否为空，为空则调用create方法
     * 并防止二次调用create方法带来的BUG
     */
    protected function isCreate($data ,$type = ''){
        if(empty($data) || !is_array($data) || empty($this->data)){//防止二次调用create
            $data = empty($this->data) ? $this->create($data ,$type) : $this->data ;
        }
        return $data;
    }
}
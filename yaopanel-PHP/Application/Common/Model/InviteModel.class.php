<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Common\Model;
use Think\Model;
class InviteModel extends YaoModel{
    
    protected $_validate  = array( //自动验证
        array('code','/^[A-Za-z0-9]{10}$/','无效码')
    );


    protected $_alias = "i"; //别名
    protected $_alias_join = "u"; //别名
    protected $_alias_join_left = "u2"; //别名
    protected $_model_join = "__USER__"; //join user表  别名
    protected $_model_join_left = "__USER__"; //left join user表  别名
    protected $_model_fk = "uid";
    protected $_model_join_pk = "id";
    
    protected static $_available = array(); //code 是否可用 ,用则存储详情，不可以则存放false

    protected function _initialize() {
        $this->_field['get'] = "$this->_alias.id,code,$this->_alias.time_create,time_use,$this->_alias_join.email,$this->_alias_join_left.email as uemail";
    }


    /*
     * 获取邀请码列表
     */
    public function getList($where, $limit = 1, $field = "") {
        $this->alias($this->_alias)
                ->field($this->_field['get'])
                ->join("$this->_model_join $this->_alias_join ON $this->_alias.$this->_model_fk=$this->_alias_join.$this->_model_join_pk")
                ->join("LEFT JOIN $this->_model_join_left $this->_alias_join_left ON $this->_alias.useid=$this->_alias_join.id")
                ->where($where)->limit($limit)->order('time_use,time_create desc');
        return $limit == 1 ? $this->find() : $this->limit($limit)->select();
    }

    /*
     * 邀请码是否有效
     * @param str $code 注意是大写的code
     */
    public function isAvailable($code){
        if(!isset(self::$_available[$code])){
            $result = $this->where(array('code'=>$code))->find();
            self::$_available[$code] = ($result && $result['useid'] == '0') ? $result : FALSE ;
        }
        return self::$_available[$code];
    }
    
    
    /*
     * 使用一个邀请码
     * 需要注意的问题：lock 是悲观锁，要结合 事务 处理释放锁，否则...
     * @param str $code 注意是大写的code
     * @param arr/str $func 回调函数 ，实现与其他模型的联动
     * @param arr $args 回调函数参数
     */
    public function useCode($code, $func='', $param_arr=array()){
        $is_code = $this->isAvailable($code);
        if($is_code){
            $is_code['useid'] = UID;
            $is_code['time_use'] = time();
            $this->startTrans();
            $extend = $func == '' ? true : call_user_func_array($func, $param_arr);
            $self   = $this->lock(TRUE)->save($is_code);
            if( $extend && $self ){
                $this->commit();
                if(isset(self::$_available[$code])){
                    unset(self::$_available[$code]);
                }
                return TRUE;
            }else{
                $this->rollback();
                return FALSE;
            }
        }
        return FALSE;
    }


    /*
     * 新增邀请码
     * @param int $num 生成邀请码的数量
     * @param int $uid 邀请码的用户id
     */
    public function build($num = 1,$uid = ''){
        if($uid == ''){
            $uid = UID;
        }
        $data = array(
            'uid'         => $uid,
            'status'      => 1,
            'time_create' => time(),
        );
        $data_save = array();
        for($i = 1; $i <= $num ; $i ++){
            $data['code'] = getGuid();
            $data_save[] = $data;
        }
        return $this->addAll($this->updateData($data_save)) ? TRUE : FALSE;
    }
    
    /*
     * 让子类在写入数据库前有机会更新数据
     * @param arr $data_save 
     * return arr
     */
    protected function updateData($data_save){
        return $data_save;
    }
    
}
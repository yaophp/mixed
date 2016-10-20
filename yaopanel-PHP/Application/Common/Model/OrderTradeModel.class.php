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
class OrderTradeModel extends YaoModel{
    
    protected $_auto = array(
        array('uid','userId',1,'callback'),
        array('benefit_id','userId',1,'callback'),
        array('trade_number','getGuid',1,'function',16),
        array('status',0),
        array('time_create','time',1,'function'),
        array('time_expire','timeExpire',1,'callback')
    );
    
    protected $_alias = "o"; //别名
    protected $_alias_u = "u"; //join user表  别名
    protected $_alias_u2 = "u2"; //left join user表  别名
    
    protected function _initialize() {
        parent::_initialize();
        $this->_field['get'] = "o.id,trade_number,detail,price,payment,status,o.time_create,time_update,time_expire,u.email,u2.email as uemail";
    }


    /*
     * 获取订单列表
     */
    public function getList($where, $limit = 1 , $field="") {
        $result = $this->alias($this->_alias)
                        ->field($this->_field['get'])
                        ->join("__USER__ $this->_alias_u ON $this->_alias.uid=$this->_alias_u.id")
                        ->join("LEFT JOIN __USER__ $this->_alias_u2 ON $this->_alias.benefit_id=$this->_alias_u2.id")
                        ->where($where)->limit($limit)->order('time_create desc')->select();
        return $result;
    }

    /*
     * 创建订单
     */
    public function build($data=array()){
        if(empty($data) || !is_array($data)){//防止二次调用create
            $data = $this->data == array() ? $this->create() : $this->data ;
        }
        return $this->add($data);
    }
    
    /*
     * 用户是否有未处理的订单
     * @param int $uid 用户id
     * @param bool $list 是否返回未处理的订单结果集，默认不返回
     */
    public function unhandle($uid , $list = FALSE){
        $where = array('uid'=>$uid , 'status'=>0);
        $count = $this->where($where)->count();
        if($list){
            $page = yao_page($count);
            $result = $this->where($where)->limit($page['limit'])->select();
        }
        return $list ? array('list'=>$result, 'page'=>$page['page'], 'count'=>$count) : $count ;
    }

    /*
     * 确定订单创建者id和 受益者id
     */
    protected function userId($args=''){
        return intval($args)== ''  ? UID : $args;
    }
    
    /*
     * 确定超时时间
     */
    protected function timeExpire(){
        $time_expire = intval(C('ORDER_EXPIRE'));
        return $time_expire ? time() + $time_expire : time() + 60*60 ;
    }
    
}

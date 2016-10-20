<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Admin\Controller;
class OrderController extends BaseController{
    
    /* 这里比较特别，因为对应的模型是OrderTrade，所以显式定义 */
    protected $_model = "OrderTrade"; //对应的模型
    protected $_model_alias = "o";//模型的别名 
    protected $_deny_action = array('edit','add');

    protected function indexFor() {
        switch(I('get.type')){
            case 'all':
                $this->_where = array("$this->_model_alias.id" => array('egt',1));
                $this->_assign['meta']  = '全部';
                return TRUE;
            case 'success':
                $this->_where = array("$this->_model_alias.time_create"=>array('gt', time_begin()),'status' => 1);
                $this->_assign['meta']  = '本月成交';
                return TRUE;
            case 'fail':
                $this->_where = array("$this->_model_alias.time_create"=>array('gt', time_begin()),'status'=> -1);
                $this->_assign['meta']  = '本月弃单';
                return TRUE;
            case 'kwd':
                return $this->searchOf();
            default : 
                $this->_where = array("$this->_model_alias.time_create"=>array('gt', time_begin()));
                $this->_assign['meta']  = '本月新增';
                return TRUE;
        }
    }

    protected function searchOf(){
        $kwd      = trim(I('get.kwd'));
        $this->_assign['kwd']    = $kwd;
        $this->_assign['meta']   = '搜索到用户';
        if(empty($kwd) || !preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', $kwd) ||  !$user = M('user')->where(array('email'=>$kwd))->find() ){
            return FALSE;
        }else{
            $this->_where = array("$this->_model_alias.uid" => $user['id']);
            return TRUE;
        }
    }

}

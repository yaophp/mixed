<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Admin\Controller;

class InviteController extends BaseController{
    protected $_model_alias = "i"; //对应的主模型 别名
    /*
     * 管理员一键生成码
     */
    public function add(){
        $num = intval(I('get.num'));
        if($num <= 0 || $num > 100){
            $num = 10;
        }
        $tb_invi = D($this->_model);
        $tb_invi -> build($num,UID) ? $this->success("成功生成{$num}个码") : $this->error($tb_invi->getError()) ;
    }
    
    protected function indexFor(){
        switch(I('get.type')){
            case 'all':
                $this->_where = array("$this->_model_alias.id" => array('egt',1));
                $this->_assign['meta']  = '全部';
                return TRUE;
            case 'used':
                $this->_where = array('useid' => array('gt',0));
                $this->_assign['meta']  = '所有已用';
                 return TRUE;
            case 'used_month':
                $this->_where = array('time_use'=>array('gt', time_begin()));
                $this->_assign['meta']  = '本月已用';
                return TRUE;
            case 'kwd':
                return $this->searchOf();
            default : 
                $this->_where = array('useid' => 0);
                $this->_assign['meta']  = '未用';
                 return TRUE;
        }
    }
    
    protected function searchOf() {
        $usertype = trim(I('get.usertype'));
        $kwd      = trim(I('get.kwd'));
        if($usertype != 'uid'){
            $this->_assign['usertype']='select';
        }
        $this->_assign['kwd']    = $kwd;
        $this->_assign['meta']   = '搜索到用户';
        if(empty($kwd) || !preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', $kwd) ||  !$user = M('user')->where(array('email'=>$kwd))->find() ){
            return FALSE;
        }else{
            $this->_where =  $usertype == 'uid' ? array("$this->_model_alias.uid" => $user['id']) : array("$this->_model_alias.useid" => $user['id']);
            return TRUE;
        }
    }
    
}

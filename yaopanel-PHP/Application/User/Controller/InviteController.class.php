<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Controller;
class InviteController extends BaseController{
    
    protected $_assign = array(//前端基本输出的数据
        'list'  => '',
        'count' => 0,
        'meta'  => '',
        'page'  => ''
        );
    protected $_model = "Invite"; //对应的模型
    protected $_model_alias = "i";//模型的别名 
    
    public function index(){
        switch(I('get.type')){
            case 'all':
                $where = array("$this->_model_alias.uid" => UID);
                $meta  = '全部';
                break;
            case 'used':
                $where = array("$this->_model_alias.uid" => UID,'useid' => array('gt',0));
                $meta  = '所有已用';
                break;
            case 'used_month':
                $where = array("$this->_model_alias.uid" => UID, 'time_use'=>array('gt', time_begin()));
                $meta  = '本月已用';
                break;
            default : 
                $where = array("$this->_model_alias.uid" => UID,'useid' => 0);
                $meta  = '未用';
        }
        $tb_invite = D($this->_model);
        $count = $tb_invite->alias($this->_model_alias)->where($where)->count();
        $page = yao_page($count);
        if($count > 0){
            $result = $tb_invite->getList($where, $page['limit']);
            $data = array('list'=>$result, 'page'=>$page['page'], 'count'=>$count,'kwd'=>'' , 'meta'=>$meta);
        }else{
            $data = $this->_assign;
            $data['meta'] = $meta;
        }
        
        $this->assign($data);
        $this->display();
    }
    
}
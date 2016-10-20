<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace User\Controller;

class OrderController extends BaseController{
    protected $_assign = array(//前端基本输出的数据
        'list'  => '',
        'count' => 0,
        'meta'  => '',
        'page'  => ''
        );
    protected $_model = "OrderTrade"; //对应的模型
    protected $_model_alias = "o";//模型的别名 
    
    public function index(){
        switch(I('get.type')){
            case 'all':
                $where = array("$this->_model_alias.uid" => UID);
                $meta  = '全部';
                break;
            case 'success':
                $where = array("$this->_model_alias.uid" => UID,"$this->_model_alias.time_create"=>array('gt', time_begin()),'status' => 1);
                $meta  = '本月成交';
                break;
            case 'fail':
                $where = array("$this->_model_alias.uid" => UID,"$this->_model_alias.time_create"=>array('gt', time_begin()),'status'=> -1);
                $meta  = '本月弃单';
                break;
            default : 
                $where = array("$this->_model_alias.uid" => UID,"$this->_model_alias.time_create"=>array('gt', time_begin()));
                $meta  = '本月新增';
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
    
    /*
     * 放弃订单
     */
    public function giveUp(){
        $id = intval(I('get.id'));
        $tb_order = M('order_trade');
        if((FALSE != $result = $tb_order->where(array('id'=>$id))->find()) && ($result['uid'] == UID) &&($result['status'] == 0)){
            $result['status'] = -1;
            $tb_order->save($result) ? $this->success('操作成功') : $this->error('操作失败');
        }else{
            $this->error('操作失败');
        }
    }
    
    /*
     * 付款
     */
    public function topay(){
        $this->error('支付宝连接出错，请联系管理员');
    }
}

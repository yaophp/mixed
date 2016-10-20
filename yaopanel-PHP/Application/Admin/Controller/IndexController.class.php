<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Admin\Controller;
class IndexController extends BaseController{
    public function index(){
//        session('count_base',NULL);
//        $count = session('count_base');
        $count = '';
        if(!$count){
            $time_begin = time_begin();
            $count_node = M('node')->count();
            $count_user = M('user')->where(array('time_create'=>array('gt',$time_begin) ,'enable'=>array('egt',0)))->count();
            $count_orde = M('order_trade')->where(array('time_create'=>array('gt',$time_begin)))->count();
            $count_invi = M('invite')->where(array('time_use'=>array('gt',$time_begin)))->count();
            $count = array(
                'node'  => $count_node,
                'user'  => $count_user,
                'order' => $count_orde,
                'invite'=> $count_invi
            );
//            session(array('count_base'=>'session_id','expire'=>3600));
//            session('count_base',$count);
        }
        $this->assign($count);
        $this->display();
    }
    
}

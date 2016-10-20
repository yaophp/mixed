<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

/*
 * Step流程抽象类
 * 使用方法：
 * 子类继承这个类，然后设置 $_step 的值，格式为call_user_func 或 call_user_func_array的参数
 */

namespace Common\Controller;
use Think\Controller;
abstract class StepController extends Controller{
    protected $_step = array();//要运行的步骤有序集合，回调格式的参数
    protected $_break = false;//true 则中断运行
    protected $_type  = "step";//默认模板参数和I()取值参数，用于判断运行哪个step，和调用哪个模板
    protected $_prefix = "YaoPHP_STEP_";//step session 前缀避免冲突
    protected $_assign = array();//用于模板赋值
    protected $_display = "";//如果设置该值，则会使用该模板


    public function step(){
        $step = $this->stepFor();
        if( !$step || $this->_break ){
            header("Content-type:text/html;charset=utf-8");
            exit('非法操作');
        }else{
            $key = key($step);
            call_user_func_array($step[$key],array());
            session($this->_prefix .CONTROLLER_NAME .(string)$key , 'true');
            $this->assign($this->_assign);
            $this->display($this->_display == "" ? $this->_type . (string)$key : $this->_display);
        }
    }
    
    /*
     * 处理输入数据
     */
    protected function stepFor() {
        $type = intval(I($this->_type,0));
        if(isset($this->_step[$type]) && !empty($this->_step[$type])){
            //检查上一步是否执行
            foreach($this->_step as $key => $value){
                $before = (string)($key-1);
                if($key > 0 && !session($this->_prefix .CONTROLLER_NAME .$before)){
                    $this->_break = TRUE; 
                    return FALSE;
                }
                if($key == $type){
                    return array($key => $this->_step[$key]);
                }
            }
        }else{
            $this->_break = TRUE;
            return FALSE;
        }
    }
    
    /*
     * 跳转到第几步
     */
    protected function stepTo($stepNum){
        redirect(U(CONTROLLER_NAME . "/step", array($this->_type => intval($stepNum))));
    }

}
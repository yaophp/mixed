<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Home\Controller;
use Think\Controller;
abstract class BaseController extends Controller {
    protected $_assign = array(
        'list' => '',
        'count' => 0,
        'page' => ''
    );

    protected function _initialize(){
//        站点关闭维护
        site_close();
    }
    
    
    /*
     * 登录检查
     */
    protected function checkLogin(){
        define('UID',  is_login());
        if(!UID){
            session('return_url',__SELF__);//登录后跳转返回页面
            $this->error('请登录后操作!',U('User/Public/login'),1);
        }
    }
    
    protected function jump404(){
        header("http/1.1 404 not found");
        redirect(C("ERROR_PAGE"));
    }
}
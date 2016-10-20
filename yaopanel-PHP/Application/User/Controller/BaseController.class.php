<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Controller;
use Think\Controller;
abstract class BaseController extends \Common\Controller\YaoController{
    protected function _initialize(){
        parent::_initialize();
        site_close();
        define('UID', is_login());
        if(!UID){
            $this->redirect('Public/login');
        }
    }
    
    protected function indexFor() {
        $this->jump404();
    }
    protected function addFor() {
        $this->jump404();
    }
    protected function editFor() {
        $this->jump404();
    }
    protected function lookFor() {
        $this->jump404();
    }
}
<?php

/*
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Admin\Controller;

abstract class BaseController extends \Common\Controller\YaoController {
    protected $_assign = array(//基本前端输出的数据
        'list'  => '', //数据列表
        'count' => 0, //数据总数
        'kwd'   => '', //关键字
        'meta'  => '',//标签
        'page'  => ''//分页
        );
    protected function _initialize() {
        parent::_initialize();

        define('UID', is_login());
        if (!UID) {
            $this->redirect('Public/login');
        }
        if (!is_admin(UID)) {
            $this->error('未授权访问');
        }
        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $api = new \Common\Api\ConfigApi;
            $config = $api->lists();
            S('DB_CONFIG_DATA', $config);
        }
        C($config); //添加配置        
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
    
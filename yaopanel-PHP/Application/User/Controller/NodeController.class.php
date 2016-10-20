<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Controller;

class NodeController extends BaseController{
    
    protected $_assign = array(
        'list'=>'',
        'page'=>'',
        'count'=>0,
    );

    public function indexFor() {
        return TRUE;
    }
    
}
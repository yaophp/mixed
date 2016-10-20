<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Home\Controller;
class PostController extends BaseController{
    public function look(){
        $tb_post = M('post');
        $this->_assign['list'] = $tb_post ->field('id,title,content,time_update')-> where(array('id'=>intval(I('get.id'))))->find();
        if(empty($this->_assign['list'])){
            $this->jump404();
        }else{
            $this->assign($this->_assign);
            $this->display();
        }
        
    }
    
}
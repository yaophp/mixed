<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Admin\Controller;
class NodeController extends BaseController{
    
    protected function indexFor() {
        return TRUE;
    }

    /*
     * 编辑节点
     */
    public function edit(){
        $tb_node = M('node');
        if(IS_POST){
            $data = I('post.','','strip_tags,htmlspecialchars');
            $id = intval($data['id']);
            unset($data['id']);
            if($id > 0 ){
                $tb_node->where(array('id'=> $id))->save($data) ? $this->success('节点修改成功',U('Node/index')) : $this->error('节点修改失败') ;
            }
        }else{
            $id = intval(I('get.id'));
            $result = $id >0 ? $tb_node->where(array('id'=> $id))->find() : '' ;
            $this->assign('node',$result);
            $this->display();
        }
    }
    
    /*
     * 新增节点
     */
    public function add(){
        if(IS_POST){
            $data = I('post.','','strip_tags,htmlspecialchars');
            $tb_node = M('node');
            $tb_node->field('name,server,method,info,type,status,order')->add($data) ? $this->success('新增节点成功',U('Node/index')) : $this->error('新增节点失败') ;
        }else{
            $this->display();
        }
    }
    
    /*
     * 删除节点
     */
    public function delete(){
        $id = intval(I('get.id'));
        $tb_node = M('node');
        $tb_node -> where(array('id'=>$id))->delete() ? $this->success('节点删除成功'): $this->error('节点删除失败');
    }
}
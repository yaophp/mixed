<?php
namespace Home\Controller;

class IndexController extends BaseController {
    public function index(){
        $product = S('product');
        if(!$product){
            $product = M('product')->field('id,name,transfer,monthly')->where('status=1')->select();
            S('product',$product,60*60*24);
        }
        
        $post = M('post')->field('id,title,time_update')->where('status=1')->order('time_create desc,id desc')->limit(5)->select();
        $this->assign('product',$product);
        $this->assign('list',$post);
        $this->display();
    }
    
}
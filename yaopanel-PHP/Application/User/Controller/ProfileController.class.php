<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Controller;
class ProfileController extends BaseController{
    public function index(){
        if(IS_POST){
            
        }else{
            $tb_profile = M('profile');
            $result = $tb_profile -> where(array('uid'=> UID)) -> find();
            $this->assign($result);
            $this->display();
        }
    }
    
    
    public function edit(){
        if(IS_POST){
            //todo
        }else{
            $tb_profile = M('profile');
            $result = $tb_profile -> where(array('uid'=> UID)) -> find();
            $this->assign('list',$result);
            $this->display();
        }
    }
    
    public function editPWD(){
        if(IS_POST){
            $pwd = array(
                'old' => trim(I('post.old')),
                'new' => trim(I('post.new')),
                're'  => trim(I('post.re'))
            );
            if($pwd['old'] == '' || $pwd['new'] == ''|| $pwd['re'] == ''){
                $this->error('请填写完整信息');
            }elseif($pwd['new'] != $pwd['re']){
                $this->error('两次新密码输入不一致');
            }else{
                foreach($pwd as $v){
                    if( !preg_match('/^[A-Za-z0-9_]{6,16}$/', $v)){
                        $this->error('密码由6-16个数字、大小写字母、下划线组成');
                        exit;
                    }
                }
            }
            $tb_user = D('User');
            $tb_user -> resetPwd($pwd['old'],$pwd['new']) ? $this->success('修改密码成功',U('Index/index')) : $this->error($tb_user->getError());
        }else{
            $this->display();
        }
    }
}
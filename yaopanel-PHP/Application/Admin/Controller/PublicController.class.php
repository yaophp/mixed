<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller{
    /*
     * 用户登录
     */
    public function login(){
        if(IS_POST){
            $data = array(
                'email' => trim(I('post.email')),
                'passwd'=> trim(I('post.pwd'))
            );
            if($data['email'] == '' || $data['passwd'] == ''){
                $this->error('请填写完整登录信息');
            }
            $tb_user = D('User');
            if($tb_user->create($data,2)){
                $login_id = $tb_user->login($data['email'],$data['passwd']);
                if($login_id ){//管理员登录
                    is_admin($login_id) ? $this->success('登录成功，即将跳转...',U('Index/index')) : $this->error('未授权访问后台！');
                }
                $this->error($tb_user->getError());
            }
            $this->error($tb_user->getError());
        }else{
            $this->display();
        }
    }
    
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
        session(null);
        $this->success('退出登录成功',U('Public/login'));
    }
    
    
}
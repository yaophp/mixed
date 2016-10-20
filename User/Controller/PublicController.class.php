<?php

/* 
 * +----------------------------------------------------------------------
 * | yao-[ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao <YaoPHP@163.com> 2016
 * +----------------------------------------------------------------------
 */


namespace User\Controller;

use Think\Controller;

class PublicController extends Controller{





    /*
     * 验证码
     */
    public function verify(){
        session_start();
        verify_build();
    }
    
    /*
     * 用户登录
     * @param $str $user 手机号/邮箱/用户名
     * @param $str $pass 密码
     * @param str $type mobile-手机 email-邮箱 name-用户名  
     */
    public function login(){
        if(IS_POST){
            $user = trim(I('post.user',''));
            $pass = trim(I('post.pass',''));
            if(empty($user) || empty($pass)){
                $this->error('帐号或密码不能为空');
            }
            $tb_user = D('User');
            $uid = $tb_user->exists($user,$pass);
            if($uid){
                $data = $tb_user->updateLogin($uid, I('post.remember') ? 604800 : 0);
                $data ? $this->assign('ssotalk',$data) && $this->display('sso') : $this->error('登录时出错，请重试');
            }else{
                $this->error($tb_user->getError());
            }
        }else{
            $this->display();
        }
    }
    
    /*
     * 用户注册
     */
    public function register(){
        if(IS_POST){
            $user = array_filter(array(
                'mobile' => trim(I('post.mobile', '')),
                'email' => trim(I('post.email', '')),
                'name' => trim(I('post.name', ''))
            ));
            empty($user) || !isset($user['name']) && $this->error('帐号信息填写不完整');
            $user['password'] = I('post.pass', '');
            empty($user['password']) && $this->error('帐号信息填写不完整');
            $tb_user = D('User');
            if ($tb_user->create($user)) {
                $uid = $tb_user->add();
                if ($uid > 0) { //注册成功时自动登录，如果自动登录失败，转入手动登录
                    $data = $tb_user->updateLogin($uid);
                    $data ? $this->assign('ssotalk',$data) && $this->display('sso') : $this->success('注册成功!', U('Public/login'));
                } else {
                    $this->error('发生错误，用户注册失败');
                }
            } else {
                $this->error($tb_user->getError());
            }
        }else{
            $this->display();
        }
        
    }
    
}

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
class PublicController extends Controller {
    
    protected function _initialize(){
        site_close();
    }

    /*
     * 获取验证码
     */
    public function getVerify(){
        verify_build();
    }
    
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
                $this->loginJump($tb_user, $data);
            }
            $this->error($tb_user->getError());
        }else{
            $this->display();
        }
    }
    
    /*
     * 用户注册
     */
    public function register(){
        if(C('USER_ALLOW_REGISTER') == FALSE){
            $this->error('为了保证用户使用体验，暂时关闭注册');exit;
        }
        
        if(IS_POST){
            $data = array(
                'email'    => trim(I('post.email')),
                'passwd'   => trim(I('post.pwd')),
                'pass'     => trim(I('post.pwd')),
                'repasswd' => trim(I('post.repwd')),
                'verify'   => trim(I('post.verify'))
            );
            if($data['email'] == '' || $data['passwd'] == '' || $data['repasswd'] == '' || $data['verify'] == ''){
                $this->error('请填写完整注册信息');
            }
            
            if(!verify_check($data['verify'])){
                $this->error('验证码错误');
            }
            
            $tb_user = D('User');
            if($tb_user->create($data)){
                $tb_user -> add() ? $this->loginJump($tb_user, $data) : $this->error('用户注册失败，请稍候重试');
            }
            $this->error($tb_user->getError());
        }else{
            $this->display();
        }
    }
    
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
        session('is_invite',null);
        session('user_name',null);
        $this->success('退出登录成功',U('Home/Index/index'));
    }
    
    /*
     * 登录跳转，实现1、注册后自动登录，2、登录后跳转回源地址或面板
     * @param obj $tb_user 用户表的 D('User')对象
     * @param arr $data    用户的 email、密码
     */
    protected function loginJump($tb_user, $data) {
        $referer = session('return_url');
        session('return_url', null);
        $tb_user->login($data['email'], $data['passwd']) ? $this->success('操作成功，即将跳转...', $referer ? $referer : U('Index/index')) : $this->error('账号或密码错误！');
    }

}

<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Admin\Controller;
class UserController extends BaseController{
    protected $_assign = array(//基本前端输出的数据
        'list'  => '', //数据列表
        'count' => 0, //数据总数
        'kwd'   => '', //关键字
        'meta'  => '',//标签
        'page'  => ''//分页
        );
    //覆盖父类的_where，user处理的基本条件
    protected $_where = array('enable'=>array('gt',0));

    protected function indexFor() {
        switch (I('get.type')){
            case 'y':
                $where['time_create'] = array('gt',time_begin('y'));
                $this->_assign['meta']  = '本年新用户';
                break;
            case 'm':
                $where['time_create'] = array('gt',time_begin('m'));
                $this->_assign['meta']  = '本月新用户';
                break;
            case 'kwd':
                $kwd = trim(I('get.kwd'));
                $this->_assign['kwd'] = $kwd;
                $this->_assign['meta'] = '搜索到用户';
                if(empty($kwd) || !preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', $kwd)){
                    return FALSE;
                }else{
                    $where['email'] = $kwd;
                }
                break;
            default :
                $where['time_create'] = array('gt',0);
                $this->_assign['meta']  = '总用户';
        }
        return $this->_where = array_merge($where,  $this->_where);
    }

    /*
     * 编辑用户
     */
    public function edit(){
        $tb_user = M('user');
        if(IS_POST){
            $data = array(
                'id'              => intval(I('post.id','','strip_tags,htmlspecialchars')),
                'passwd'          => trim(I('post.passwd','','strip_tags,htmlspecialchars')),
                'port'            => intval(I('post.port','','strip_tags,htmlspecialchars')),
                'transfer_enable' => intval(I('post.transfer_enable','','strip_tags,htmlspecialchars')),
            );
            if($data['id'] === '' || $data['passwd'] === '' || $data['transfer_enable'] === '' || $data['port'] === '' ){
                $this->error('非法数据');
            }
            
            if(!preg_match('/^[A-Za-z0-9_]{6,16}$/', $data['passwd'])){
                $this->error('密码由6-16个数字、大小写字母、下划线组成');
            }
            if($data['transfer_enable'] < 0 ){
                $data['transfer_enable'] = 0;
            }
            
            if(is_admin($data['id'])){
                $this->error('禁止修改管理员身份的用户数据');
            }
            $where = array_merge(array('id'=>$data['id']),  $this->_where);
            $user = $tb_user->field('id,passwd,pass,transfer_enable,port')->where($where)->find();
            if($user){
                $data['pass'] = pwd_hash($data['passwd']);
                $data['transfer_enable'] = byte_to_unit($data['transfer_enable'], 'G', TRUE);
                if($user != $data){
                    $tb_user->where(array('id'=>$data['id']))->save($data) ? $this->success('修改用户资料成功',U('User/index')) : $this->error('用户资料修改失败');
                }else{
                    $this->success('修改用户资料成功啦',U('User/index'));
                }
               
            }else{
                $this->error('修改用户资料失败了') ;
            }
        }else{
            $where = array_merge(array('id' => intval(I('get.id'))), $this->_where);
            $result  = $tb_user->field('id,email,passwd,transfer_enable,port')->where($where)->find();
            $this->assign('user',$result);
            $this->display();
        }
    }
    
    /*
     * 删除用户
     */
    public function delete(){
        $id = intval(I('get.id'));
        $tb_user = D('User');
        $tb_user->del($id) ? $this->success('用户已成功删除') : $this->error($tb_user->getError());
    }
    
    /*
     * 修改密码
     */
    public function psw(){
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
            $tb_user -> resetPwd($pwd['old'],$pwd['new']) ? $this->success('修改密码成功') : $this->error($tb_user->getError());
        }else{
            $this->display();
        }
    }
}
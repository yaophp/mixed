<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

/*
 * 用户模型
 * 之前为了要兼容ss-panel 弄得很乱，以后优化算了
 */

namespace Common\Model;

class UserModel extends YaoModel{
    
    public $user_info = array();
    
    //自动验证
    protected $_validate = array(
        array('email','email','请填写正确的邮箱',self::EXISTS_VALIDATE ),
        array('email','','该邮箱已经被注册',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        array('email','13,25','邮箱长度13-25个字符',self::EXISTS_VALIDATE,'length'),
        
        array('passwd','/^[A-Za-z0-9_]{6,16}$/','密码由6-16个数字、大小写字母、下划线组成',self::EXISTS_VALIDATE),
        array('repasswd','passwd','两次输入的密码不一致',0,'confirm'), // 验证确认密码是否和密码一致
    );
    
    //自动完成
    protected $_auto = array(
        array('pass','pwd_hash',self::MODEL_INSERT,'function'),
        array('time_create','time',self::MODEL_INSERT,'function')
    );
    

    /*
     * 用户登录
     */
    public function login($email,$pwd){
        $result = $this->where(array('email'=>$email, 'enable'=>array('egt',0)))->find();
        if($result){
            $psw_hash = pwd_hash($pwd);
            return ($result['pass'] == $psw_hash || $result['passwd'] == $pwd ) ? $this->login_log($result) : FALSE; 
        }
        return FALSE;
    }
    

    /*
     * 修改密码
     */
    public function resetPwd($pwd_old, $pwd_new , $uid = ''){
        if($uid == ''){
            $uid = UID;
        }
        if($uid != ''){
            $where  =  array('id' => $uid) ;
            $result = $this->field('pass,passwd')->where($where)->find();
            if($result['passwd'] != $pwd_old){
                $this->error = '旧密码错误';
                return FALSE;
            }
            $data = array(
                'pass'   => pwd_hash($pwd_new),
                'passwd' => $pwd_new
            );
            return $this->where($where)->save($data);
        }
        return FALSE;
    }
    
    /*
     * 登录成功的一些操作
     * session等
     */
    protected function login_log($user){
        $auth = array(
            'email' => $user['email'],
            'time'  => time()
        );
        $auth['uid'] = $user['id'];
        /* 记录登录SESSION和COOKIES */
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        return $auth['uid'];
    }
    
    /*
     * 最近注册的用户列表
     * @param int $from_date 本月/本周/今天 00:00:00开始的时间戳
     */
    public function listRegister($from_date ,$num = 20){
        $where['time_create'] = array('egt' => $from_date);
        $count = $this->where($where)->count();
        $page = yao_page($count, $num);
        $result = $count == 0 ? '' : $this->where($where)->limit($page['limit'])->select();
        return array('list'=>$result , 'page'=>$page['page'], 'count'=> $count);
    }
    
    /*
     * 获得某用户的相关信息
     * @param str $field 某个字段，为空则全部
     * @param int $uid 默认登录者id，非空则为指定id
     */
    public function getInfo($field = '',$uid = ''){
        if($uid == ''){
            $uid = UID;
        }
        if(empty($this->user_info)){
            $this->user_info = $this->where(array('id'=>$uid))->find();
        }
        return $field=='' ? $this->user_info : $this->user_info[$field];
    }
    
    /*
     * 删除某用户，严禁删除创始人和管理员身份的人
     */
    public function del($id){
        if(is_admin($id)){
            $this->error = '不能删除管理员身份的用户';
            return FALSE;
        }
        return $this->where(array('id' => intval($id)))->setField('enable', -1) ? TRUE : FALSE;
    }
    
    /*
     * 新添有效用户。这与注册用户不同。
     * 注册用户默认没流量，没端口，这里给他加上，让用户获得服务
     * @param int $product_id 产品套餐id 默认1免费套餐 2限量套餐 3无限套餐
     * @param int $uid 用户id
     */
    public function userEnable($product_id=1, $uid=''){
        if($uid == ''){
            $uid = UID;
        }
        $data_user = array(
            'port' => $this->max('port') + rand(1, 10),//没用锁，又一定程度上降低冲突风险
            'transfer_enable' => byte_to_unit(M('product')->where(array('id'=>$product_id))->getField('transfer'), 'g', TRUE), //10G
        );
        return $this->where(array('id'=>$uid))->save($data_user);
    }
}
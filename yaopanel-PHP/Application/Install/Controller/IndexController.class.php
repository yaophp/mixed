<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Install\Controller;
use Think\Controller;
use Think\Storage;
use Think\Db;

/*
 * 安装引导
 */
class IndexController extends Controller{

    /*
     * 安装初始化
     */
    protected function _initialize(){

        //判断是否是更新
        header("Content-type: text/html; charset=utf-8"); 
        if(Storage::has(APP_PATH . 'Common/Conf/config.php') && C('SYS_VERSION') != ''){
            if(check_update()){
                session('update',TRUE);
                $msg = '请删除网站根目录的/Data/install.lock文件后再运行升级!';
            }else{
                file_put_contents('./Data/install.lock', date('Y-m-d H:i:s'));
                $this->error('系统已经是最新版，无需升级!');
            }
            
        }else{
            $msg = '请不要重复安装！为了安全，建议删除install.php文件';
        }
        //判断是否锁定安装
        if(Storage::has('./Data/install.lock')){
            $this->error($msg);
        }
        
    }

    /*
     * 服务协议
     */
    public function index(){
        session('agree',0);
        session('step',0);
        $this->display();
    }
    
    /*
     * 环境检查
     */
    public function step1(){
        $agree = I('agree');
        if($agree != 1){exit('非法操作!');}
        session('agree',$agree);
        session('error',false);
        session('step', 1);
        //检测环境
        $env = check_env();
        
        //目录文件读写检测
        if(IS_WRITE){
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }
        
        //函数检测
        $func = check_func();
        
        $this->assign('env', $env);
        $this->assign('func', $func);
        $this->display();
    }
    
    /*
     * 填写安装信息、如数据库、创始人
     */
    public function step2($db = null, $admin = null){
        (session('agree') !=1) && exit('非法操作！');
        (session('step') < 1) && exit('安装步骤错误！');
        session('error') && exit('安装环境要求未通过！');
        
        if(IS_POST){
            //检测数据库配置
//            session('step',2);
//            redirect(U('Index/step3'));
//            die;
            //检测数据库
            $this->checkDB();
            //检测管理员信息
            $this->checkAdmin();
            //跳转到数据库安装页面
            session('step',2);
            $this->redirect('step3');
        }else{
            $this->display();die;//////////////暂不考虑升级/////////////////////////
            if(session('update')){
                session('step', 2);
                $this->display('update');
            }else{
                $this->display();
            }
            
        }
        
    }
    
    /*
     * 安装过程
     */
    public function step3() {
        (session('agree') != 1) && exit('非法操作！');
        (session('step') != 2) && exit('安装步骤错误！');
        session('error') && exit('安装环境要求未通过！');
        $this->display();
        set_time_limit(100);
        
        if (session('update')) {
            $db = Db::getInstance();
            
            //更新数据表
            update_tables($db, C('DB_PREFIX'));
            //更新config 的版本号
            //todo
        } else {
            //连接数据库
            $dbconfig = session('db_config');
            $db = Db::getInstance($dbconfig);
            //创建数据表
            create_tables($db, $dbconfig['DB_PREFIX']);
            //注册创始人帐号
            $auth = build_auth_key();
            $admin = session('admin_info');
            register_administrator($db, $dbconfig['DB_PREFIX'], $admin, $auth);
            //创建配置文件
            $conf = write_config($dbconfig, $auth);
            session('config_file', $conf);
            //创建安装锁
            show_msg('正在生成安装锁...');
            file_put_contents('./Data/install.lock', date('Y-m-d H:i:s'));
            show_msg('生成安装锁成功！', 'success');
            show_msg('安装已完成', 'success');
        }
        if (session('error')) {
            show_msg('安装出错，已经中断...', 'error');
        } else {
            show_msg('即将跳转...');
            session('step',3);
            sleep(2);
            redirect(U('Ok/index'));
        }
    }

    /*
     * 检查数据库连接
     */
    public function checkDB(){
        $db = array(
            'DB_TYPE' => 'mysql',
            'DB_HOST' => I('post.dbhost'),
            'DB_NAME' => I('post.dbname'),
            'DB_USER' => I('post.dbuser'),
            'DB_PWD' => I('post.dbpw'),
            'DB_PORT' => I('post.dbport'),
            'DB_PREFIX' => '' //因为manyuser 的user表没有前缀
        );
        if (!is_array($db) || empty($db['DB_HOST']) || empty($db['DB_PORT']) || empty($db['DB_NAME']) || empty($db['DB_USER'])) {
            $this->error('请填写完整的数据库配置');
        } else {
            session('db_config', $db);
            $dbname = $db['DB_NAME'];
            unset($db['DB_NAME']);
            $DB = Db::getInstance($db);
            $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
            try {
                $DB->execute($sql);
            } catch (Exception $e) {
                $this->error($DB->getError());
            }
            
        }
    }
    
    /*
     * 管理员帐号
     */
    protected function checkAdmin(){
        $admin = array(
            'email' => I('manager_email'),
            'password' => I('manager_pwd'),
            'repassword' => I('manager_ckpwd'),
        );
        if (!is_array($admin) || empty($admin['password']) || empty($admin['repassword']) || empty($admin['email'])) {
            $this->error('请填写完整管理员信息');
        } else if ($admin['password'] != $admin['repassword']) {
            $this->error('确认密码和密码不一致');
        } elseif (preg_match('/^[A-Za-z0-9_]{6,16}$/', $admin['password']) == FALSE || preg_match('/^[A-Za-z0-9_]{6,16}$/', $admin['repassword']) == FALSE) {
            $this->error('密码由6-16个数字、大小写字母、下划线组成');
        } else {
            session('admin_info', $admin);
        }
    }
    
}

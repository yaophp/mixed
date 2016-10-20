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
class OkController extends Controller{
    
    public function index(){
        header("Content-type: text/html; charset=utf-8"); 
        (session('agree') !=1) && exit('非法操作！');
        (session('step') != 3) && exit('安装步骤错误！');
        session('error') && exit('安装环境要求未通过！');
        session(null);
        $this->display();die;
        if(Storage::has(APP_PATH . 'User/Conf/config.php') && Storage::has('./Data/install.lock')){
            $this->display();
        }else{
            $this->error('系统配置文件不存在，若重试安装后仍然出现该提示，请到官网反馈！','',99999);
        }
    }
}

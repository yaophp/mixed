<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

/*
 * 设置Api,默认配置文件Application/Common/Conf/setting.php
 * 特别有利于HTML_CACHE_ON的静态缓存设置
 */
namespace Common\Api;
use Think\Storage;

class SettingApi extends ConfigApi{
    
    protected $_settingFile = 'setting.php'; //文件名
    protected $_Settinginit = array(//默认的配置项
        'WEB_SITE_TITLE'=>'YaoJob招聘',//网站标题
        'HTML_CACHE_ON' => 0 , //静态缓存开关
    /*
     * 支付宝接口配置
     */
    //支付宝接口基本参数
	'alipay_config' => array(
		'partner'   =>  '2088101011913539',   //这里是你在成功申请支付宝接口后获取到的PID；
		'key'       =>  '7d314d22efba4f336fb187697793b9d2',//这里是你在成功申请支付宝接口后获取到的Key
		'sign_type' =>  'MD5',
                'sign'      =>  '7d314d22efba4f336fb187697793b9d2',//这里是你在成功申请支付宝接口后获取到的Key
		'_input_charset'=> 'utf-8',
	),
        //支付宝接口业务参数
	'alipay'   =>   array(
		//这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
		'seller_email' =>   'yao365@163.com',//推荐使用seller_id
		//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
		'notify_url'   =>   'http://localhost/yaojob/Company/Pay/notifyurl',
		//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
		'return_url'    =>  'http://localhost/yaojob/Company/Pay/returnurl',
		//支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
		'successpage'   =>  'User/myorder?ordtype=payed',
		//支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
		'errorpage'     =>  'User/myorder?ordtype=unpay',
	),
    ); 


    /*
     * 提供自定义配置项接口
     * 第二个参数表示是否保留默认配置项
     */
    public function setData($data = array(),$clear = 0){
        if($clear == 0){//保留默认配置项
            $this->_Settinginit = array_merge($this->_Settinginit ,(array)$data) ;
        }else{//不保留
            $this->_Settinginit = (array)$data;
        }
    }

    /*
     * 按照数据库信息，同步更新设置文件
     * 默认保留曾经手动设置配置文件的$_settinginit 的信息
     */
    public function autoUpdate($clear = 0){
        $config_old = $this->get();
        if(!isset($config_old)){
            $config_old = $this->_Settinginit;
        }
        $config_db  = $this->lists();
        $config_new = array_intersect_key($config_db, $config_old);
        $this->save($clear == 0 ? array_merge($config_old,$config_new) : $config_new);
        
    }
    
    
    /*
     * 手动读取配置
     */
    public function get(){
        if(Storage::has(CONF_PATH .$this->_settingFile)){
            $config = include CONF_PATH .$this->_settingFile;
        }else{
            $config = $this->_Settinginit;
        }
        return $config;
    }

    /*
     * 手动保存配置
     */
    public function save($config  = array()){

        $setfile = CONF_PATH . $this->_settingFile;
        $settingstr = "<?php \n return " . var_export($config, true) . "\n ?>";
        try{
            Storage::put($setfile, $settingstr); //注意权限和云平台todo///////////////////////////////////
//            file_put_contents($setfile, $settingstr); //注意权限和云平台todo///////////////////////////////////
        }catch (\Exception $e){
            exit($e->getMessage());
        }
        $this->cacheClean();
    }
    
    /*
     * 清除缓存，使更新生效
     */
    public function cacheClean(){
        try{
            if (Storage::has(RUNTIME_FILE)) {
                Storage::unlink(RUNTIME_FILE); //删除RUNTIME_FILE;
            }
            //光删除runtime_file还不够，要清空一下Temp文件夹中的文件；代码如下：
            $dh = opendir(TEMP_PATH);
            if ($dh) {     //打开Cache文件夹；
                while (($file = readdir($dh)) !== false) {    //遍历Cache目录，
                    unlink(TEMP_PATH . $file);                //删除遍历到的每一个文件；
                }
                closedir($dh);
            }
        }catch(\Exception $e){
            exit($e->getMessage());
        }
//        try{
//            if (file_exists(RUNTIME_FILE)) {
//                unlink(RUNTIME_FILE); //删除RUNTIME_FILE;
//            }
//            //光删除runtime_file还不够，要清空一下Temp文件夹中的文件；代码如下：
//            $dh = opendir(TEMP_PATH);
//            if ($dh) {     //打开Cache文件夹；
//                while (($file = readdir($dh)) !== false) {    //遍历Cache目录，
//                    unlink(TEMP_PATH . $file);                //删除遍历到的每一个文件；
//                }
//                closedir($dh);
//            }
//        }catch(\Exception $e){
//            exit($e->getMessage());
//        }
    }
    
}
<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
/*
 * 基础Controller类 ,参考 restful 的 post-添加 ，get-获取，put-更新， delete-删除
 * 以For结尾的函数要有返回，并设置$_where
 */

namespace Common\Controller;
use Think\Controller;
abstract class YaoController extends Controller{
    
    /* 模型Model相关 */
    protected $_model = ""; //对应的主模型
    protected $_model_alias = ""; //对应的主模型 别名
    protected $_where = array(); //用户提交的经过处理的数据，用于筛选
    protected $_data  = array(); //用户提交的经过处理的数据，用于修改或新增
    protected $_field = array(); //可以指定数据库返回的字段
    
    /* 模板Template相关 */
    protected $_info_s = "操作成功"; //默认操作成功信息
    protected $_info_e = "操作失败"; //默认操作失败信息
    protected $_display = ""; //模板显示,可以指定显示哪一模板
    protected $_assign = array(//模板赋值
        'list' => '', //数据列表
        'count' => 0, //数据总数
        'page' => '' //分页
    );
    
    protected function _initialize(){
        empty($this->_model) && $this->_model = CONTROLLER_NAME;
    }
    
    /*
     * 处理用户输入，对模型的数据处理是从$_where , $_data 获取
     * 1、必须有返回，用于bool判断
     * 2、必须设置$_where 及 $_data（如果是新增或修改）
     * 3、可以设置$_field 及 $_display
     */
    abstract protected function indexFor();
    abstract protected function addFor();
    abstract protected function editFor();
    abstract protected function lookFor();
    
    
    /*
     * 索引
     */
    public function index(){
        if($this->indexFor()){
            $this->getList();
        }
        $this->assign($this->_assign);
        $this->display($this->_display);
    } //索引 ,GET
    
    /*
     * 查看
     */
    public function look(){
        if($this->lookFor()){
            $this->getList(1);
        }
        $this->assign($this->_assign);
        $this->display($this->_display);
    } //查看 ,GET
    
    /*
     * 添加
     */
    public function add(){
        if(IS_POST){
            if($this->addFor()){
                $this->postList() ? $this->success($this->_info_s) : $this->error($this->_info_e) ;
            }else{
                $this->error($this->_info_e);
            }
        }else{
            $this->display($this->_display);
        }
    } //添加 ,POST
    
    /*
     * 编辑
     */
    public function edit(){
        if(IS_POST){
            if($this->editFor()){
                $this->putList() ? $this->success($this->_info_s) : $this->error($this->_info_e) ; 
            }else{
                $this->error($this->_info_e);
            }
        }else{
            if($this->editFor()){
                $this->getList(1);
            }
            $this->assign($this->_assign);
            $this->display($this->_display);
        }
    } //编辑 ,GET 、POST
    
    /*
     * 获取数据
     * 注意主键
     */
    protected function getList($limit = 20){
        $tb = D($this->_model);
        if($limit === 1){
            $result = $tb->getList($this->_where , 1 , $this->_field);
            $this->_assign['list'] = $result;
        }else{
            $count = $tb->alias($this->_model_alias)->where($this->_where)->count();//这里暂不处理主键的问题
            if($count > 0){
                $page = yao_page($count);//在这里暂不传入$limit设置分页
                $result = $tb->getList($this->_where, $page['limit'] , $this->_field);
                $this->_assign['list'] = $result;
                $this->_assign['page'] = $page['page'];
                $this->_assign['count']= $count;
            }
        }
    }
    
    /*
     * 更新数据
     * 注意主键
     */
    protected function putList(){
        return D($this->_model)->putList($this->_where,  $this->_data , $this->_field);
    }
    
    /*
     * 添加数据
     */
    protected function postList(){
        return D($this->_model)->postList($this->_data , $this->_field);
    }
    
    
    public function jump404(){
        header("http/1.1 404 not found");
        redirect(C('ERROR_PAGE'));
    }
}

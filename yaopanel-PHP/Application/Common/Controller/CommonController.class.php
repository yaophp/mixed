<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
/*
 *  统一基本格调
 * #、index、add、edit...xxx ，权限为public，供用户使用的开放方法，使用_deny_controller、_deny_action可禁用
 * #、indexOf、addOf、editOf...xxxOf ，权限为protected，用于处理用户输入的数据，例如，create方法应在此处使用，
 * #、getList、postList、putList...xxxList ，权限为protected，用于处理模型数据
 * #、模板赋值必须统一用$_assign输出，如果有自定义值，可以 array_merge
 * #、对控制器操作 index-索引，add-添加，edit-编辑，search-搜索
 * #、对模型操作参考 restful 的 postList-添加 ，getList-获取，putList-更新， deleteList-删除
 */


namespace Common\Controller;
use Think\Controller;

abstract class CommonController extends Controller{
    protected $_assign = array(//基本前端输出的数据
        'list'  => '', //数据列表
        'count' => 0, //数据总数
        'kwd'   => '', //关键字
        'meta'  => '',//标签
        'page'  => ''//分页
        );
    protected $_model = ""; //对应的模型
    protected $_model_alias = "";//模型的别名
    protected $_where = ""; //筛选条件
    protected $_field = ""; //字段
    protected $_msg_s = "操作成功";//默认操作成功信息
    protected $_msg_e = "操作失败";//默认操作失败信息
    protected $_deny_controller = '';//禁止访问输入的当前的controller，例如，BaseController，则 'Base'
    protected $_deny_action = array();//禁止访问的action，以小写的方式



    protected function _initialize(){
        
        if( (CONTROLLER_NAME == $this->_deny_controller) || (!empty($this->_deny_action) && in_array(strtolower(ACTION_NAME), $this->_deny_action))){
            redirect(C('ERROR_PAGE'));
        }
        empty($this->_model) && $this->_model = CONTROLLER_NAME;
        empty($this->_model_alias) && $this->_model_alias = strtolower(substr($this->_model, 0,1));
        
    }
    
    /*
     * index方法
     */
    public function index(){
        $this->indexOf();
        $this->getList();
        $this->assign($this->_assign);
        $this->display();
    }
    
    /*
     * 搜索方法
     */
    public function search(){
        if($this->searchOf()){
            $this->getList();
        }
        $this->assign($this->_assign);
        $this->display('index');//这里如果优化的话，可以考虑灵活性
    }
    
    /*
     * 添加方法
     */
    public function add(){
        if(IS_POST){
            if( TRUE == $dataList = $this->addOf()){
                $this->postList($dataList) ? $this->success($this->_msg_s) : $this->error($this->_msg_e) ;
            }else{
                $this->error($this->_msg_e);
            }
        }else{
            $this->display();
        }
    }
    
    public function edit(){
        if(IS_POST){
            if(TRUE == $dataList = $this->editOf()){
                $this->putList($dataList) ? $this->success($this->_msg_s) : $this->error($this->_msg_e) ; 
            }else{
                $this->error($this->_msg_e);
            }
        }else{
            if($this->editOf()){
                $this->getList();
            }
            $this->assign($this->_assign);
            $this->display();
        }
    }
    
    
    /*
     * 从模型获取数据，并将结果赋值给$_assign
     * list 、page 、 count
     */
    protected function getList(){
        $tb = D($this->_model);
        $count = $tb->alias($this->_model_alias)->where($this->_where)->count();//这里暂不处理主键的问题
        if($count > 0){
            $page = yao_page($count);
            $result = $tb->getList($this->_where, $page['limit']);
            $this->_assign['list'] = $result;
            $this->_assign['page'] = $page['page'];
            $this->_assign['count']= $count;
        }
    }
    
    /*
     * 向模型添加数据
     * 参考 restful 的 post-添加 ，get-获取，put-更新， delete-删除
     */
    protected function postList($dataList = array()){
        return D($this->_model)->postList($dataList);
    }
    
    /*
     * 更新模型数据
     * 注意主键
     */
    protected function putList($dataList = array()){
        return D($this->_model)->putList($this->_where,$dataList);
    }

    /*
     * 需要实现meta，where , kwd(如果有)
     * 将赋值给$_assign 和 $_where 
     * 例如 下例子
     */
    protected function indexOf(){
        return true;
    }
    
    /*
     * 
     * 搜索的关键字, 的用户输入数据过滤
     * 无效或非法提交则返回false
     * 这里是对应邀请码的示例，其他可根据情况重载
     */
    protected function searchOf(){
        return TRUE;
    }
    
    /*
     * 添加资源 的用户输入数据过滤
     */
    protected function addOf(){
        return TRUE;
    }
    
    /*
     * 编辑资源 的用户输入数据过滤
     */
    protected function editOf(){
        if(IS_POST){
            $id = intval(I('post.id'));
            //与子类的基本_where 合并
            $this->_where = is_array($this->_where) ? array_merge($this->_where,array("id"=>$id)) : array("id"=>$id);
            $data = D($this->_model)->create(I('post.'));
            if($data){
                return $data;
            }else{
                $this->error($this->_msg_e);
            }
        }else{
            $id = intval(I('get.id'));
            //与子类的基本_where 合并
            $this->_where = is_array($this->_where) ? array_merge(array("id"=>$id),$this->_where) : array("id"=>$id);
            return TRUE;
        }
    }
}

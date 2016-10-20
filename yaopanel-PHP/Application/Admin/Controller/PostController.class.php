<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Admin\Controller;

class PostController extends BaseController{
    //覆盖父类的_where，user处理的基本条件
    protected $_model_alias = 'p';
    protected $_join_alias = 'u';
    protected $_where = array('status'=>1);

    protected function indexFor() {
        switch (I('get.type')){
            case 'notify':
                $where['catalog'] = 'notify';
                $this->_assign['meta']  = '最新公告';
                break;
            case 'help':
                $where['catalog'] = 'help';
                $this->_assign['meta']  = '使用帮助';
                break;
            case 'other':
                $where['catalog'] = 'other';
                $this->_assign['meta']  = '其他分类';
                break;
            case 'post_month':
                $where["$this->_model_alias.time_create"] = array('gt', time_begin());
                $this->_assign['meta']  = '本月发布';
                break;
            default :
                $where = array();
                $this->_assign['meta']  = '全部';
        }
        $this->_field = "$this->_model_alias.id,title,catalog,$this->_model_alias.time_create,$this->_model_alias.time_update,$this->_join_alias.email";
        return $this->_where = array_merge($where,  $this->_where);
    }
    
    protected function addFor() {
        if(IS_POST){
            return $this->_data = I('post.');
        }
        return TRUE;
    }
    
    protected function editFor() {
        if(IS_POST){
            $this->_where = array_merge(array("id" => intval(I('get.id'))),  $this->_where);
            return $this->_data = I('post.');
        }else{
            $this->_field = "$this->_model_alias.id,title,catalog,content,time_update";
            return $this->_where = array_merge(array("$this->_model_alias.id" => intval(I('get.id'))),  $this->_where);
        }
    }
    
    /*
     * umeditor 图片上传
     */
    public function upload(){
        $upload = new \Think\Upload(C('UPLOAD_PICTURE_UMEDITOR'));
        $info = $upload->upload();
        if ($info) {
            // 上传成功 获取上传文件信息  
            foreach ($info as &$file) {
                //拼接出文件相对路径
                $file['filepath'] = $file['savepath'] . $file['savename'];
            }
            //返回json数据被百度Umeditor编辑器   
            exit( json_encode(array(
                'url' => $file['filepath'],
                'title' => htmlspecialchars($_POST['pictitle'], ENT_QUOTES), 'original' => $file['savename'],
                'state' => 'SUCCESS'
            )) );
        } else {
            // 上传失败   
            exit( json_encode(array('state' => $upload->getError())) );
        }
    }
}
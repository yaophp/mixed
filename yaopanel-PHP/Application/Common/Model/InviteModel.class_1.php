<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Common\Model;
use Think\Model;
class InviteModel extends Model{
    
    protected $_data_base = array(
        'uid'         => '',
        'status'      => 1,
        'time_create' => '',
    );
    
    protected $_alias = "i";
    protected $_model_join = "u";
    protected $_field   = "i.id,code,i.time_create,time_use,u.email,u2.email as uemail";

    
    
    
    /*
     * 获取验证码列表
     */
    public function getList($where, $limit){
        $result = $this->alias($this->_alias_i)
                ->field($this->_field)
                ->join("__USER__ u ON $this->_alias_i.uid=u.id")
                ->join("LEFT JOIN __USER__ u2 ON $this->_alias_i.useid=u2.id")
                ->where($where)->limit($limit)->order('time_use,time_create desc')->select();
        return  $result;
    }
    
    /*
     * 新增邀请码
     * @param int $num 生成邀请码的数量
     * @param int $uid 邀请码的用户id
     */
    public function build($num = 1,$uid = ''){
        $data = $this->setData($uid);
        $data_save = array();
        for($i = 1; $i <= $num ; $i ++){
            $data['code'] = getGuid();
            $data_save[] = $data;
        }
        return $this->addAll($data_save) ? TRUE : FALSE;
    }
    
    protected function setData($uid = ''){
        if($uid == ''){
            $uid = UID;
        }
        return $this->_data_base = array(
            'uid'         => $uid,
            'status'      => 1,
            'time_create' => time(),
        );
    }
}
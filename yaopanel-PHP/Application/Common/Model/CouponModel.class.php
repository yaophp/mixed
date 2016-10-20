<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */


namespace Common\Model;

class CouponModel extends InviteModel{

    protected $_off = array(//概率百分比
        '5'  => 5,
        '10' => 15,
        '15' => 25,
        '20' => 25,
        '25' => 15,
        '35' => 6,
        '40' => 5,
        '45' => 3,
        '50' => 1 //例如，获得 50 off 的概率是百分一
    );
    protected $_off2 = array(//概率千分比
        '5'  => 50,
        '10' => 100,
        '15' => 550,
        '20' => 100,
        '25' => 100,
        '35' => 50,
        '40' => 30,
        '45' => 15,
        '50' => 5 //例如，获得 50 off 的概率是 千分之五
    );
    
    protected $_alias = "c";
    
    protected function _initialize() {
        $this->_field['get'] = "$this->_alias.id,code,off,time_expire,$this->_alias.time_create,time_use,$this->_alias_join.email,$this->_alias_join_left.email as uemail";
    }


    /*
     * 更新本类的数据
     */
    protected function updateData($data_save) {
        $time = time() + C('COUPON_EXPIRE');
        $rand = C('COUPON_RAND_TYPE') == 0 ? $this->_off : $this->_off2 ;//百分/千分
        foreach ($data_save as & $v){
            $v['off'] = get_rand($rand);
            $v['time_expire'] = $time;
        }
        unset($v);
        return $data_save;
    }





    /*
     * 获取当前用户/某用户的推广码
     */
//    public function get($uid = ''){
//        if($uid == ''){
//            $uid = UID;
//        }
//        return $this->where(array('uid'=>$uid))->find();
//    }
    
    /*
     * 检查优惠码是否存在
     * 若存在，返回优惠off，不存在false; 
     */
//    public function check($code){
//        $length = strlen($code);
//        if($length > 0 && $length < 128){
//            $result = $this->where(array('code'=> $code))->find();
//            if($result){
//                return $result['off'];
//            }
//        }
//        return FALSE;
//    }
    
    /*
     * 优惠码生成/重新生成
     */
//    public function build($uid = ''){
//        if($uid == ''){
//            $uid = UID;
//        }
//        $time_create = time();
//        $data = array(
//            'off' => get_rand($this->_off),
//            'time_create' => $time_create,
//            'time_expire' => $time_create + C('COUPON_EXPIRE')
//        );
//        $result = $this->get($uid);
//        if($result){//已经生成过
//            if($time_create -  C('COUPON_REBUILD') > $result['time_create']){
//                $data['id'] = $result['id'];
//                return $this->save($data);
//            }
//        }else{//第一次生成
//            $data['uid'] = $uid;
//            $data['code']= substr(think_ucenter_md5($uid), 0,8);
//            return $this->add($data);
//        }
//        return FALSE;
//    }
    
}
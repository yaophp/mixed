<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
    /*
     * 获取验证码
     */
    public function getVerify(){
        verify_build();
    }
}

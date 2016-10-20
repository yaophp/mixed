<?php

/*
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace User\Controller;

class IndexController extends BaseController {

    public function index() {
        if (is_admin(UID)) {
            $plan = '管理员';
        } else {
            switch (TRUE) {
                case M('order_trade')->where(array('uid' => UID, 'status' => 1))->find():
                    $plan = '付费会员';
                    break;
                case M('invite')->where(array('useid' => UID))->find():
                    $plan = '免费会员';
                    break;
                default :
                    $plan = '新注册用户';
            }
        }

        $tb_user = D('User');
        $result = $tb_user->getInfo();
        $percent = round(($result['u'] + $result['d']) / $result['transfer_enable'], 2) * 100;

        $user_index = array_merge(array('percent' => $percent, 'plan' => $plan), $result);
        $this->assign($user_index);
        $this->display();
    }

}

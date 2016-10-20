<?php

/*
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

namespace Home\Controller;

class CouponController extends BaseController {
    /*
     * 优惠码是否有效
     */

    public function isAvailable() {
        if (IS_POST) {
            if(!C('SWITCH_COUPON_USE')){
                session('code_coupon', null);
                $this->error('优惠码暂时不能使用');
            }
            $code = strtoupper(trim(I('post.coupon')));
            $tb_coupon = D('Coupon');
            if ($tb_coupon->create(array('code' => $code)) && $result = $tb_coupon->isAvailable($code)) {
                session('code_coupon', $code);
                $this->success($result['off']);
            } else {
                session('code_coupon', null);
                $this->error('无效码');
            }
        }
    }

}

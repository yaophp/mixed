<?php

/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
namespace Home\Controller;

class CartController extends BaseController{
    
    protected $_product = array();

    protected function _initialize() {
        
        parent::_initialize();
        $this->checkLogin();
        
        $this->_product = $this->productCheck();
    }
    
    public function index(){
//        $is_product = $this->productCheck();
        $is_product = $this->_product;
        
        $this->assign('product',$is_product);
        $is_product['name'] == '免费版' ? $this->display('free') : $this->display();
    }
    
    
    /*
     * 确定订单信息,在下一步生成订单前，可以输入优惠码
     */
    public function confirm(){
        $id   = intval(I('get.id'));
        $plan = I('get.plan');
        if(empty($id) || empty($plan)){
            redirect(C('ERROR_PAGE'));die;
        }
        $product = $this->detail($plan);
        $this->assign('plan',$plan);
        $this->assign('product',$product);
        session('order_confirm', getGuid(32));
        $this->display();
    }
    
    /*
     * 生成订单，并跳转支付
     */
    public function checkout(){
        $plan = I('post.plan');
        if(!session('order_confirm') || $this->_product['id']==1 || !isset($this->_product[$plan])){
            exit;
        }
        $product = $this->detail($plan);
        $data = array(
            'price' => $product['price'],
            'detail'=> $product['detail'] ." 总计：￥ " .$product['price'] . "人民币"
        );
        $tb_order_trade = D('OrderTrade');
        $data_save = $tb_order_trade->create($data);
        $tb_coupon = D('Coupon');
        if( (FALSE != $code = is_use_coupon()) && (FALSE != $result_code = $tb_coupon->isAvailable($code))){
            $data_save['price'] = round($data_save['price'] * (100 - $result_code['off']) / 100);//算了，不要小数点
            $data_save['detail'] .= " ,因使用优惠码：$code ，折后：￥" . $data_save['price'] . "人民币";
            $result = $tb_coupon->useCode($code , array($tb_order_trade ,'build'),array($data_save));
        }else{
            $result = $tb_order_trade->build($data_save);
        }
        
        $result ? $this->error('支付宝连接失败，请联系网站管理员',U('User/Order/index')) : $this->error('订单创建失败');
        
    }
    
    /*
     * 申请免费版本
     */
    public function invite(){
        
//        $this->productCheck(1);
        if(IS_POST){
            $code = strtoupper(trim(I('post.code')));
            $verify = trim(I('post.verify'));
            if($code=='' || $verify == ''){
                $this->error('请填写完整信息');
            }elseif(verify_check($verify)){
                $this->error('验证码错误');
            }elseif(!preg_match('/^[A-Za-z0-9]{10}$/', $code)){
                $this->error('邀请码格式错误');
            }
            $tb_invite = D('Invite');
            if(!$tb_invite->isAvailable($code)){
                $this->error('邀请码无效');
            }
            if($tb_invite->useCode($code, array(D('User'), 'userEnable'))){
                session('is_invited',null);
                $this->success('申请成功',U('User/Index/index'));
            }else{
                $this->error('发生意外，若问题继续出现，请联系管理员');
            }
        }else{
            $this->display();
        }
    }
    
    
    /*
     * 判断是否已经申请过免费版
     */
    protected function is_invited(){
        $data = session('is_invited');
        if(!$data){
            $result = M('invite')->where(array('useid'=>UID))->find();
            $data = $result ? 'yes' : 'no';
            session('is_invited',$data);
        }
        return $data == 'yes'? TRUE : FALSE;
    }
    
    /*
     * 订单摘要
     */
    protected function detail($plan){
        $product = $this->_product;
        switch ($plan){
            case 'annually':
                $product['payment'] = '年付';
                $product['price'] = $product['annually'];
                break;
            case 'quarterly':
                $product['payment'] = '季付';
                $product['price'] = $product['quarterly'];
                break;
            default :
                $product['payment'] = '月付';
                $product['price'] = $product['monthly'];
        }
        $transfer = $product['transfer'] == 9999 ? '不限流量': $this->_product['transfer'] ."G流量/月";
        $product['detail'] = $product['name'] ."——" .$transfer . "——" . $product['payment'] ; 
        return $product;
    }



    /*
     * 检查产品是否有效，注意不要在initialize里用，
     * 大概是因为I('get.id')要兼容 免费版 的某个操作？？忘记了，
     * 话说，把免费版归入这里弄得乱乱的，后续版本考虑独立出去todo
     * 
     */
    protected function productCheck($pid = ''){
        if($pid ==''){
           $pid = intval(I('id')) == '' ? 1 : intval(I('id'))  ; 
        }
//        empty($pid) && redirect(C('ERROR_PAGE'));
        $tb_product = M('product');
        $is_product = $tb_product->where(array('id'=>$pid , 'status'=>1))->find();
        if(!$is_product){
            $this->error('该套餐已经下架或已删除。');
        }
        if($is_product['id'] == 1 || $is_product['name'] == '免费版'){
            if(C('SWITCH_PRODUCT_FREE') != 1){
                $this->error('因大量用户涌入，为保证服务质量，暂停申请。请留意最新公告，过一段时间再来',U('Index/index'),15);
                die;
            }elseif(TRUE == $this->is_invited()){
                $this->error('每位用户限申请一次，请把机会留给其他有需要的人',U('Index/index'),5);
            }
        }
        return $is_product;
    }

}

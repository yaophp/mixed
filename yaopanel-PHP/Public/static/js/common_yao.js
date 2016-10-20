/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */

$().ready(function () {
    //Tab菜单高亮
    var active = $(".nav-tabs").find('li');
    var meta   = $("#meta").text();
    $.each(active, function () {
        var li = $(this);
        if (li.text() == meta) {
            li.addClass('active');
            return false;
        }
    });
    
    //消除优惠码提示
    $(".coupon_input").focus(function(){
        $('.coupon_tips').text("");
    });
    
    //优惠码
    $("#coupon_check").click(function(){
        is_ajax($(this) ,function(data){
//            var data = {status:1};
            if(data.status == 1){
                var price_old = $('.price_count').text();
                var off = data.info;
                var price_new = Math.round(price_old * (100-off) / 100);
                $('.price_old').css("text-decoration","line-through");
                $('.price_up').text(price_new);
                $('.price_new').css('display','block');
                $('.coupon_tips').css('visibility','visible').text("已享受 -"+off+"% 优惠");
            }else{
                var result = data.info ? data.info : "无效码!";
                $('.coupon_tips').css('visibility','visible').text(result);
                $('.price_new').css('display','none');
                $('.price_old').css("text-decoration","none");
            }
        });
    });
    
    
});



/*
 * 函数：通用ajax方式
 * @param {type} obj
 * @param {type} func 回调自定义函数，默认调用success
 * @returns {Boolean}
 */
function is_ajax(obj,func){
    var target,method,form,data;
    var that = obj;
    if ( that.hasClass('is_confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
    }
    var callback = arguments[1] ? func : success; 
    if((target = that.attr('href'))||(target = that.attr('url'))||(that.attr('type')=='submit')){
        method = that.attr('target-form') ? 'post' : 'get';
        $(this).ajaxSend(function(){
            that.addClass('disabled').attr('autocomplete','off');
        });
        if(method == 'get'){//get
            $.get(target,callback);
        }else{//post
            form = $('.'+ that.attr('target-form'));
            if(form.attr('action')!= undefined){
                target = form.attr('action');
            }
            data = form.serialize();
            $.post(target,data,callback);
        }
        
        $(this).ajaxComplete(function(){
            that.removeClass('disabled').attr('autocomplete','on');
        });
        return false;
    }
    function success(data){
        if(data.status==1){//成功操作
            var msg= data.info == '' ? '操作成功！': data.info;
            layer.msg(msg, {icon: 1, time: 1500});
            if(data.url){
                setTimeout(function(){
                    location.href=data.url;
                },2000);
            }
        }else{//失败操作
            that.removeClass('disabled').prop('disabled',false);
            var msg = data.info == '' ? '操作失败！': data.info;
            layer.msg(msg, {icon: 2, time: 3000});
        }
    }
}

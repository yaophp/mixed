/* 
 * +----------------------------------------------------------------------
 * | [ FOR SAVING TIME ].
 * +----------------------------------------------------------------------
 * | Author: yao - <yao365@163.com> 2016
 * +----------------------------------------------------------------------
 */
(function(){
    if(window._thinkAd){return}
    window._thinkAd=true;
    document.createElement("thinkad");
    var thinkAdConfig={"api":"http://ad.topthink.com/api/basic","jsPath":"http://ad.topthink.com/Public/static/","defaultApi":"basic"};
    var thinkHelper={};
    thinkHelper.head=document.getElementsByTagName("head")[0];
    thinkHelper.emptyFunc=function(){};
    thinkHelper.getScript=function(scriptUrl,cache){
        var cache=arguments[1]?arguments[1]:false;
        var success=thinkHelper.emptyFunc;
        var error=thinkHelper.emptyFunc;
        var script=document.createElement("script");
        script.onload=function(){
            success();
            success=thinkHelper.emptyFunc
        };
        script.onreadystatechange=function(){
            if(this.readyState&&this.readyState==="loaded"){
                success();
                success=thinkHelper.emptyFunc
            }
        };
        script.onerror=function(){
            error();
            error=thinkHelper.emptyFunc
        };
        script.type="text/javascript";
        if(!cache){
            scriptUrl=thinkHelper.addQueryParams(scriptUrl,{"_t":Math.random()})
        }
        script.src=scriptUrl;
        thinkHelper.head.appendChild(script);
        var result={
            success:function(func){
                success=func;
                return result
            },
            error:function(func){
                error=func;
                return result}
        };
        return result
    };
    thinkHelper.getStyle=function(cssUrl,cache){
        var cache=arguments[1]?arguments[1]:false;
        var link=document.createElement("link");
        link.rel="stylesheet";link.type="text/css";
        if(!cache){
            cssUrl=thinkHelper.addQueryParams(cssUrl,{"_t":Math.random()})
        }
        link.href=cssUrl;
        thinkHelper.head.appendChild(link)
    };
    thinkHelper.addCssString=function(cssString){
        var style=document.createElement("style");
        style.setAttribute("type","text/css");
        if(style.styleSheet){
            style.styleSheet.cssText=cssString
        }else{
            var cssText=document.createTextNode(cssString);
            style.appendChild(cssText)
        }
        var heads=document.getElementsByTagName("head");
        if(heads.length){
            heads[0].appendChild(style)
        }else{
            document.documentElement.appendChild(style)
        }
    };
    thinkHelper.addQueryParams=function(url,obj){
        var joinChar=(url.indexOf("?")===-1)?"?":"&";
        var arrParams=[];
        for(var key in obj){
            arrParams.push(key+"="+encodeURIComponent(obj[key]))
        }
        return url+joinChar+arrParams.join("&")
    };
    thinkHelper.jsonp=function(url,success,error){
        var error=error?error:thinkHelper.emptyFunc;
        var callback="callback_"+Math.random().toString().replace(".","_");
        window[callback]=success;
        url=thinkHelper.addQueryParams(url,{callback:callback});
        thinkHelper.getScript(url).error(error)};
    thinkHelper.setAd=function(thinkAdDom){
        var api=thinkAdConfig.api+"/"+thinkAdDom.id;
        thinkHelper.jsonp(api,function(data){
            if(typeof data!=="object"||!data.on){
                thinkAdDom.style.display="none";
                return
            }
            thinkAdDom.style.display="block";
            if(data.width){
                thinkAdDom.style.width=data.width+"px"
            }
            if(data.height){
                thinkAdDom.style.height=data.height+"px"
            }
            thinkAdDom.style.overflow="hidden";
            if(data.css_file){
                var cssFiles=data.css_file.split("|");
                for(var i in cssFiles){
                    thinkHelper.getStyle(cssFiles[i])
                }
            }
            if(data.css_code){
                thinkHelper.addCssString(data.css_code)
            }
            thinkAdDom.innerHTML=data.html;
            var isJsFileOk=false;
            if(data.js_file){
                var jsFiles=data.js_file.split("|");
                var jsFileCount=0;
                for(var i in jsFiles){
                    thinkHelper.getScript(jsFiles[i]).success(function(){
                        jsFileCount++;
                        if(jsFileCount>=jsFiles.length){
                            isJsFileOk=true
                        }
                    }
                            )
                }
            }else{
                isJsFileOk=true
            }
            var n=setInterval(function(){
                if(isJsFileOk){
                    clearInterval(n);
                    eval("(function(){try{"+data.js_code+"}catch(e){}})();")
                }
            },100)})
    };
    var st=null;
    var num=0;
    var parse={};
    var _loop=function(){
        if(num<50){
            var thinkAds=document.getElementsByTagName("thinkad");
            for(var i=0;i<thinkAds.length;i++){
                if(!parse[thinkAds[i].id]){
                    parse[thinkAds[i].id]=1;
                    thinkAds[i].setAttribute("parse",1);
                    thinkHelper.setAd(thinkAds[i])}}num++
        }else{
            parse=null;
            clearInterval(st)
        }
    };
    st=setInterval(_loop,200)})();


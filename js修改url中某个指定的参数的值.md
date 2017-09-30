当一个页面上的搜索条件很多而且需要进行联合get查询的时候，我们按照一定的规则将其组织为一个正确的url这是没有问题的，但是当这个联合查询可以无限制的进行下去的话，你该如何组织你的url呢？无限制的在当前的url后面附加你的查询参数吗？很显然这是不科学的。下面分享一个办法可以直接修改当前url中的某个参数的值而不会导致这个url中同一个参数存在多个值的情况。这在一定程度上就将url的长度控制到了最短。既人性化又利于搜索引擎的优化。下面是利用javascript修改url中某个参数的值的具体思路：

/* 
* url 目标url 
* arg 需要替换的参数名称 
* arg_val 替换后的参数的值 
* return url 参数替换后的url 
*/ 
function changeURLArg(url,arg,arg_val){ 
    var pattern=arg+'=([^&]*)'; 
    var replaceText=arg+'='+arg_val; 
    if(url.match(pattern)){ 
        var tmp='/('+ arg+'=)([^&]*)/gi'; 
        tmp=url.replace(eval(tmp),replaceText); 
        return tmp; 
    }else{ 
        if(url.match('[\?]')){ 
            return url+'&'+replaceText; 
        }else{ 
            return url+'?'+replaceText; 
        } 
    } 
    return url+'\n'+arg+'\n'+arg_val; 
}

使用方法如下：
changeURLArg('http://www.daimajiayuan.com/test.php?class_id=3&id=2','class_id',4); 

结果即为：http://www.daimajiayuan.com/test.php?class_id=4&id=2

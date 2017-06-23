<?php


  /**
 * 异步执行函数
 * @param $url
 * @param array $post_data
 * @param array $cookie
 * @return bool
 */
function triggerRequest($url, $post_data = array(), $cookie = array()){
    $method = "GET";  //可以通过POST或者GET传递一些参数给要触发的脚本
    $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
    $port = isset($url_array['port'])? $url_array['port'] : 80;
    $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
    if (!$fp){
        return FALSE;
    }
    if(isset($url_array['query'])){
        $getPath = $url_array['path'] ."?". $url_array['query'];
    }else{
        $getPath = $url_array['path'];
    }

    if(!empty($post_data)){
        $method = "POST";
    }
    $header = $method . " " . $getPath;
    $header .= " HTTP/1.1\r\n";
    $header .= "Host: ". $url_array['host'] . "\r\n "; //HTTP 1.1 Host域不能省略
    $header .= "Connection:Close\r\n\r\n";
    if(!empty($cookie)){
        $_cookie = strval(NULL);
        foreach($cookie as $k => $v){
            $_cookie .= $k."=".$v."; ";
        }
        $cookie_str =  "Cookie: " . base64_encode($_cookie) ." \r\n";//传递Cookie
        $header .= $cookie_str;
    }
    if(!empty($post_data)){
        $_post = strval(NULL);
        foreach($post_data as $k => $v){
            $_post .= $k."=".$v."&";
        }
        $post_str  = "Content-Type: application/x-www-form-urlencoded\r\n";//POST数据
        $post_str .= "Content-Length: ". strlen($_post) ." \r\n";//POST数据的长度
        $post_str .= $_post."\r\n\r\n "; //传递POST数据
        $header .= $post_str;
    }
    fwrite($fp, $header);
    fclose($fp);
    return true;
}



    public function ceshi(){
        $url='http://127.0.0.1/index.php/index/Common/doit';
        triggerRequest($url);//异步执行
        return "hello";
    }

    /**
     * 异步执行的方法
     */
    public function doit(){
        ignore_user_abort();//忽略用户拒绝
        set_time_limit(0);//无限时间
        #####################################
        sleep(3);
        file_put_contents(RUNTIME_PATH."/ceshi".time().".txt","ceshi");
    }

function send_post($url, $post_data) {
    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 0,// 超时时间（单位:s）
            'Connection'=>'close'
        )
    );
    $context = stream_context_create($options);
    @file_get_contents($url, false, $context);
}

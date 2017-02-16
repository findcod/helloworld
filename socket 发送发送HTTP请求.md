socket 发送发送HTTP请求

socket方式：

复制代码
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>20, "usec"=>0));
socket_connect($socket, 'www.baidu.com', 80);

//里面的换行代表 \r\n 注意拷贝的代码后面可能有空格
$http = <<<eof
GET / HTTP/1.0
Accept: */*
User-Agent: Lowell-Agent
Host: www.baidu.com
Connection: Close

eof;

socket_write($socket, $http, strlen($http));

while($str = socket_read($socket, 1024))
{
    echo $str;
}

socket_close($socket);
复制代码

fsockopen方式：

复制代码
$fp = fsockopen("www.baidu.com", 80, $errno, $errstr, 30);

if (!$fp) {
    echo "$errstr ($errno)
\n";
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: www.baidu.com\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $http);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}
复制代码

原始socket方式：

复制代码
$fp = stream_socket_client("tcp://www.baidu.com:80", $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)
\n";
} else {

    $http = <<<eof
GET / HTTP/1.0
Accept: */*
User-Agent: Lowell-Agent
Host: www.baidu.com
Connection: Close

eof;

    fwrite($fp, $http);
    while (!feof($fp)) {
        echo fgets($fp, 1024);
    }
    fclose($fp);
}
复制代码

stream  方式（get）：

复制代码
$http = <<<eof
Host: www.baidu.com
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3
Cookie: BAIDUID=79D98B1AD8436C57B967E111E484F1CD:FG=1; BDUSS=lF-UFFOanFPVG92NmF4U3NiTEoxOFh4YVBCTnZaMUtoTUNhZmxrWThwN25IaUJVQVFBQUFBJCQAAAAAAAAAAAEAAADzo1gKc2lxaW5pYW8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOeR-FPnkfhTU; BAIDUPSID=79D98B1AD8436C57B967E111E484F1CD; BD_UPN=13314352; BD_HOME=1; H_PS_PSSID=10047_1435_10874_10212_10501_10496_10753_10796_10219_10355_10666_10597_10095_10658_10442_10700_10460_10360_10618; sug=3; sugstore=0; ORIGIN=2; bdime=0
Connection: keep-alive
Cache-Control: max-age=0
eof;

$hdrs = array(
        'http' =>array(
                'header' => $http,
                'timeout'=>1, //超时 秒
                'method' => 'GET', //默认方式
　　　　　　　　　'protocol_version' => '1.1', //默认为 1.0
        ),

);

//参数格式参考 http://php.net/manual/zh/context.http.php
//curl方式的格式可以参考； http://php.net/manual/zh/context.curl.php

$context = stream_context_create($hdrs);
echo file_get_contents('http://www.baidu.com', 0, $context);
复制代码

stream  方式 post:

复制代码
$postdata = http_build_query(array('act'=>'save', 'id'=>387171));

$http = <<<eof
Host: www.baidu.com
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3
Content-Type: application/x-www-form-urlencoded; charset=UTF-8 
Cookie: BAIDUID=79D98B1AD8436C57B967E111E484F1CD:FG=1; BDUSS=lF-UFFOanFPVG92NmF4U3NiTEoxOFh4YVBCTnZaMUtoTUNhZmxrWThwN25IaUJVQVFBQUFBJCQAAAAAAAAAAAEAAADzo1gKc2lxaW5pYW8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOeR-FPnkfhTU; BAIDUPSID=79D98B1AD8436C57B967E111E484F1CD; BD_UPN=13314352; BD_HOME=1; H_PS_PSSID=10047_1435_10874_10212_10501_10496_10753_10796_10219_10355_10666_10597_10095_10658_10442_10700_10460_10360_10618; sug=3; sugstore=0; ORIGIN=2; bdime=0
Connection: keep-alive
Cache-Control: max-age=0
eof;

#注意post方式需要增加Content-Type

$hdrs = array(
        'http' =>array(
                'header' => $http,
                'timeout'=>1, //超时 秒
                'method' => 'POST',
                'content' => $postdata,
　　　　　　　　　'protocol_version' => '1.1', //默认为 1.0
        ),

);

//参数格式参考 http://php.net/manual/zh/context.http.php
//curl方式的格式可以参考； http://php.net/manual/zh/context.curl.php

$context = stream_context_create($hdrs);
echo file_get_contents('http://test.cm/song.php', 0, $context);
复制代码

注意：http1.1 中必须包含 Host 头， 而 http1.0中则可以没有

HTTP超文本传输协议-HTTP/1.1中文版

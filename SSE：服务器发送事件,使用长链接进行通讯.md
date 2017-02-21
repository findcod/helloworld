<div id="post_detail">

<div class="singlepost">

<div class="posttitle">[SSE：服务器发送事件,使用长链接进行通讯](http://www.cnblogs.com/goody9807/p/4257192.html)</div>

<div id="cnblogs_post_body">

<div class="chapter">

## 概述

</div>

传统的网页都是浏览器向服务器“查询”数据，但是很多场合，最有效的方式是服务器向浏览器“发送”数据。比如，每当收到新的电子邮件，服务器就向浏览器发送一个“通知”，这要比浏览器按时向服务器查询（polling）更有效率。

服务器发送事件（Server-Sent Events，简称SSE）就是为了解决这个问题，而提出的一种新API，部署在EventSource对象上。目前，除了IE，其他主流浏览器都支持。

简单说，所谓SSE，就是浏览器向服务器发送一个HTTP请求，然后服务器不断单向地向浏览器推送“信息”（message）。这种信息在格式上很简单，就是“信息”加上前缀“data: ”，然后以“\n\n”结尾。

<div class="highlight">

```
$ curl http://example.com/dates
data: 1394572346452

data: 1394572347457

data: 1394572348463

^C
```

</div>

SSE与WebSocket有相似功能，都是用来建立浏览器与服务器之间的通信渠道。两者的区别在于：

*   WebSocket是全双工通道，可以双向通信，功能更强；SSE是单向通道，只能服务器向浏览器端发送。

*   WebSocket是一个新的协议，需要服务器端支持；SSE则是部署在HTTP协议之上的，现有的服务器软件都支持。

*   SSE是一个轻量级协议，相对简单；WebSocket是一种较重的协议，相对复杂。

*   SSE默认支持断线重连，WebSocket则需要额外部署。

*   SSE支持自定义发送的数据类型。

从上面的比较可以看出，两者各有特点，适合不同的场合。

<div class="chapter">

## 客户端代码

</div>

### 概述

首先，使用下面的代码，检测浏览器是否支持SSE。

<div class="highlight">

```
if (!!window.EventSource) {
  // ...
}
```

</div>

然后，部署SSE大概如下。

<div class="highlight">

```
var source = new EventSource('/dates');

source.onmessage = function(e){
  console.log(e.data);
};

// 或者

source.addEventListener('message', function(e){})
```

</div>

### 建立连接

首先，浏览器向服务器发起连接，生成一个EventSource的实例对象。

<div class="highlight">

```
var source = new EventSource(url);
```

</div>

参数url就是服务器网址，必须与当前网页的网址在同一个网域（domain），而且协议和端口都必须相同。

下面是一个建立连接的实例。

<div class="highlight">

```
if (!!window.EventSource) {
  var source = new EventSource('http://127.0.0.1/sses/');
}
```

</div>

新生成的EventSource实例对象，有一个readyState属性，表明连接所处的状态。

<div class="highlight">

```
source.readyState
```

</div>

它可以取以下值：

*   0，相当于常量EventSource.CONNECTING，表示连接还未建立，或者连接断线。

*   1，相当于常量EventSource.OPEN，表示连接已经建立，可以接受数据。

*   2，相当于常量EventSource.CLOSED，表示连接已断，且不会重连。

### open事件

连接一旦建立，就会触发open事件，可以定义相应的回调函数。

<div class="highlight">

```
source.onopen = function(event) {
  // handle open event
};

// 或者

source.addEventListener("open", function(event) {
  // handle open event
}, false);
```

</div>

### message事件

收到数据就会触发message事件。

<div class="highlight">

```
source.onmessage = function(event) {
  var data = event.data;
  var origin = event.origin;
  var lastEventId = event.lastEventId;
  // handle message
};

// 或者

source.addEventListener("message", function(event) {
  var data = event.data;
  var origin = event.origin;
  var lastEventId = event.lastEventId;
  // handle message
}, false);
```

</div>

参数对象event有如下属性：

*   data：服务器端传回的数据（文本格式）。

*   origin： 服务器端URL的域名部分，即协议、域名和端口。

*   lastEventId：数据的编号，由服务器端发送。如果没有编号，这个属性为空。

### error事件

如果发生通信错误（比如连接中断），就会触发error事件。

<div class="highlight">

```
source.onerror = function(event) {
  // handle error event
};

// 或者

source.addEventListener("error", function(event) {
  // handle error event
}, false);
```

</div>

### 自定义事件

服务器可以与浏览器约定自定义事件。这种情况下，发送回来的数据不会触发message事件。

<div class="highlight">

```
source.addEventListener("foo", function(event) {
  var data = event.data;
  var origin = event.origin;
  var lastEventId = event.lastEventId;
  // handle message
}, false);
```

</div>

上面代码表示，浏览器对foo事件进行监听。

### close方法

close方法用于关闭连接。

<div class="highlight">

```
source.close();
```

</div>

<div class="chapter">

## 数据格式

</div>

### 概述

服务器端发送的数据的HTTP头信息如下：

<div class="highlight">

```
Content-Type: text/event-stream
Cache-Control: no-cache
Connection: keep-alive
```

</div>

后面的行都是如下格式：

<div class="highlight">

```
field: value\n
```

</div>

field可以取四个值：“data”, “event”, “id”, or “retry”，也就是说有四类头信息。每次HTTP通信可以包含这四类头信息中的一类或多类。\n代表换行符。

以冒号开头的行，表示注释。通常，服务器每隔一段时间就会向浏览器发送一个注释，保持连接不中断。

<div class="highlight">

```
: This is a comment
```

</div>

下面是一些例子。

<div class="highlight">

```
: this is a test stream\n\n

data: some text\n\n

data: another message\n
data: with two lines \n\n
```

</div>

### data：数据栏

数据内容用data表示，可以占用一行或多行。如果数据只有一行，则像下面这样，以“\n\n”结尾。

<div class="highlight">

```
data:  message\n\n
```

</div>

如果数据有多行，则最后一行用“\n\n”结尾，前面行都用“\n”结尾。

<div class="highlight">

```
data: begin message\n
data: continue message\n\n
```

</div>

总之，最后一行的data，结尾要用两个换行符号，表示数据结束。

以发送JSON格式的数据为例。

<div class="highlight">

```
data: {\n
data: "foo": "bar",\n
data: "baz", 555\n
data: }\n\n
```

</div>

### id：数据标识符

数据标识符用id表示，相当于每一条数据的编号。

<div class="highlight">

```
id: msg1\n
data: message\n\n
```

</div>

浏览器用lastEventId属性读取这个值。一旦连接断线，浏览器会发送一个HTTP头，里面包含一个特殊的“Last-Event-ID”头信息，将这个值发送回来，用来帮助服务器端重建连接。因此，这个头信息可以被视为一种同步机制。

### event栏：自定义信息类型

event头信息表示自定义的数据类型，或者说数据的名字。

<div class="highlight">

```
event: foo\n
data: a foo event\n\n

data: an unnamed event\n\n

event: bar\n
data: a bar event\n\n
```

</div>

上面的代码创造了三条信息。第一条是foo，触发浏览器端的foo事件；第二条未取名，表示默认类型，触发浏览器端的message事件；第三条是bar，触发浏览器端的bar事件。

### retry：最大间隔时间

浏览器默认的是，如果服务器端三秒内没有发送任何信息，则开始重连。服务器端可以用retry头信息，指定通信的最大间隔时间。

<div class="highlight">

```
retry: 10000\n
```

</div>

<div class="chapter">

## 服务器代码

</div>

服务器端发送事件，要求服务器与浏览器保持连接。对于不同的服务器软件来说，所消耗的资源是不一样的。Apache服务器，每个连接就是一个线程，如果要维持大量连接，势必要消耗大量资源。Node.js则是所有连接都使用同一个线程，因此消耗的资源会小得多，但是这要求每个连接不能包含很耗时的操作，比如磁盘的IO读写。

下面是Node.js的服务器发送事件的[代码实例](http://cjihrig.com/blog/server-sent-events-in-node-js/)。

<div class="highlight">

```
var http = require("http");

http.createServer(function (req, res) {

    var fileName = "." + req.url;

    if (fileName === "./stream") {
        res.writeHead(200, {"Content-Type":"text/event-stream", 
                            "Cache-Control":"no-cache", 
                            "Connection":"keep-alive"});
        res.write("retry: 10000\n");
        res.write("event: connecttime\n");
        res.write("data: " + (new Date()) + "\n\n");
        res.write("data: " + (new Date()) + "\n\n");

        interval = setInterval(function() {
            res.write("data: " + (new Date()) + "\n\n");
        }, 1000);

        req.connection.addListener("close", function () {
            clearInterval(interval);
        }, false);
  }
}).listen(80, "127.0.0.1");
```

</div>

PHP代码实例。

<div class="highlight">

```
<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // 建议不要缓存SSE数据

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 */
function sendMsg($id, $msg) {
  echo "id: $id" . PHP_EOL;
  echo "data: $msg" . PHP_EOL;
  echo PHP_EOL;
  ob_flush();
  flush();
}

$serverTime = time();

sendMsg($serverTime, 'server time: ' . date("h:i:s", time()));
```

</div>

<div class="chapter">

## 参考链接

</div>

*   Colin Ihrig, [Implementing Push Technology Using Server-Sent Events](http://jspro.com/apis/implementing-push-technology-using-server-sent-events/)
*   Colin Ihrig，[The Server Side of Server-Sent Events](http://cjihrig.com/blog/the-server-side-of-server-sent-events/)
*   Eric Bidelman, [Stream Updates with Server-Sent Events](http://www.html5rocks.com/en/tutorials/eventsource/basics/)
*   MDN，[Using server-sent events](https://developer.mozilla.org/en-US/docs/Server-sent_events/Using_server-sent_events)
*   Segment.io, [Server-Sent Events: The simplest realtime browser spec](https://segment.io/blog/2014-04-03-server-sent-events-the-simplest-realtime-browser-spec/)
*   [http://javascript.ruanyifeng.com/htmlapi/eventsource.html](http://javascript.ruanyifeng.com/htmlapi/eventsource.html)

</div>

<div id="blog_post_info_block">

<div id="BlogPostCategory">分类: [Js与DHtml](http://www.cnblogs.com/goody9807/category/18732.html)</div>

<div id="blog_post_info">

<div id="green_channel">[好文要顶](javascript:void(0);) [关注我](javascript:void(0);) [收藏该文](javascript:void(0);) [![](//common.cnblogs.com/images/icon_weibo_24.png)](javascript:void(0); "分享至新浪微博") [![](//common.cnblogs.com/images/wechat.png)](javascript:void(0); "分享至微信")</div>

<div id="author_profile">

<div id="author_profile_info" class="author_profile_info">[![](//pic.cnblogs.com/face/u2507.jpg)](http://home.cnblogs.com/u/goody9807/)

<div id="author_profile_detail" class="author_profile_info">[PointNet](http://home.cnblogs.com/u/goody9807/)
[关注 - 1](http://home.cnblogs.com/u/goody9807/followees)
[粉丝 - 264](http://home.cnblogs.com/u/goody9807/followers)</div>

</div>

<div id="author_profile_follow">[+加关注](javascript:void(0);)</div>

</div>

<div id="div_digg">

<div class="diggit" onclick="votePost(4257192,'Digg')"><span class="diggnum" id="digg_count">1</span></div>

<div class="buryit" onclick="votePost(4257192,'Bury')"><span class="burynum" id="bury_count">0</span></div>

</div>

</div>

<div id="post_next_prev">[«](http://www.cnblogs.com/goody9807/p/4244862.html) 上一篇：[fastjson生成和解析json数据，序列化和反序列化数据](http://www.cnblogs.com/goody9807/p/4244862.html "发布于2015-01-23 18:34")
[»](http://www.cnblogs.com/goody9807/p/4261269.html) 下一篇：[Comet：基于 HTTP 长连接的“服务器推”技术](http://www.cnblogs.com/goody9807/p/4261269.html "发布于2015-01-29 23:10")
</div>

</div>

<div class="itemdesc">posted on <span id="post-date">2015-01-28 21:46</span> [PointNet](http://www.cnblogs.com/goody9807/) 阅读(<span id="post_view_count">3446</span>) 评论(<span id="post_comment_count">3</span>) [编辑](https://i.cnblogs.com/EditPosts.aspx?postid=4257192) [收藏](#)</div>

</div>

<script type="text/javascript">var allowComments=true,cb_blogId=5395,cb_entryId=4257192,cb_blogApp=currentBlogApp,cb_blogUserGuid='bc86310b-63cf-dd11-9e4d-001cf0cd104b',cb_entryCreatedDate='2015/1/28 21:46:00';loadViewCount(cb_entryId);</script></div>

<a name="!comments"></a>

<div id="blog-comments-placeholder">评论:

<div class="moreinfo">

*   <div class="moreinfotitle">[#1楼](#3511895)<a name="3511895" id="comment_anchor_3511895"></a>  [橙子cookie](http://www.cnblogs.com/cookiecjj/) [ ](http://msg.cnblogs.com/send/%E6%A9%99%E5%AD%90cookie "发送站内短消息") Posted @ <span class="comment_date">2016-09-17 16:14</span></div>

    <div id="comment_body_3511895" class="blog_comment_body">你好，想问下，我看了下sse，默认每隔3秒会给服务端发送请求，而我自己ajax轮训也是设置每隔时间去请求，我看了两个的请求头很像，消耗资源差不多，那么使用sse的优势到底是什么呢？不用自己写定时器了？麻烦解答下</div>

    <div class="comment_vote">[支持(0)](javascript:void(0);)[反对(1)](javascript:void(0);)</div>

      <span class="comment_actions"></span>

*   <div class="moreinfotitle">[#2楼](#3561081)<a name="3561081" id="comment_anchor_3561081"></a>  [yafeng](http://www.cnblogs.com/yafengabc/) [ ](http://msg.cnblogs.com/send/yafeng "发送站内短消息") Posted @ <span class="comment_date">2016-11-21 13:34</span></div>

    <div id="comment_body_3561081" class="blog_comment_body">[@](#3511895 "查看所回复的评论") 橙子cookie
    关键是要long polling
    3秒钟只是断线重连的时间。
    因为你服务器不支持长连接，请求一次直接反返回了。所以3秒钟重连一次</div>

    <div class="comment_vote">[支持(0)](javascript:void(0);)[反对(0)](javascript:void(0);)</div>

      <span class="comment_actions"></span>

*   <div class="moreinfotitle">[#3楼](#3561086)<a name="3561086" id="comment_anchor_3561086"></a><span id="comment-maxId" style="display:none;">3561086</span><span id="comment-maxDate" style="display:none;">2016/11/21 13:36:50</span>  [yafeng](http://www.cnblogs.com/yafengabc/) [ ](http://msg.cnblogs.com/send/yafeng "发送站内短消息") Posted @ <span class="comment_date">2016-11-21 13:36</span></div>

    <div id="comment_body_3561086" class="blog_comment_body">LZ的PHP例子貌似也不是长连接的，估计也是3秒连一次（我不懂PHP的长连接机制，说的可能不对），那个node的例子大约是没问题的</div>

    <div class="comment_vote">[支持(0)](javascript:void(0);)[反对(0)](javascript:void(0);)</div>

      <span class="comment_actions"></span>

</div>

</div>

<script type="text/javascript">var commentManager = new blogCommentManager();commentManager.renderComments(0);</script>

<div id="comment_form" class="commentform"><a name="commentform"></a>

<div id="comment_nav"><span id="span_refresh_tips"></span>[刷新评论](javascript:void(0);)[刷新页面](#)[返回顶部](#top)</div>

<div id="comment_form_container">

<div class="login_tips">注册用户登录后才能发表评论，请 [登录](javascript:void(0);) 或 [注册](javascript:void(0);)，[访问](http://www.cnblogs.com)网站首页。</div>

</div>

<div id="under_post_news">

<div class="itnews c_ad_block">**最新IT新闻**:
· [Linux占领世界，Windows支配桌面](http://news.cnblogs.com/n/563319/)
· [小米做自主芯片 这是一场结局难料的豪赌](http://news.cnblogs.com/n/563335/)
· [治疗失眠的机器人枕头即将诞生 不用再吃安眠药](http://news.cnblogs.com/n/563334/)
· [唯品会第四季度净利1.106亿美元 同比增长51.7%](http://news.cnblogs.com/n/563333/)
· [受Note 7影响 三星在声誉排名中已从第三跌至第四十九](http://news.cnblogs.com/n/563332/)
» [更多新闻...](http://news.cnblogs.com/ "IT新闻")</div>

</div>

<div id="under_post_kb">

<div class="itnews c_ad_block" id="kb_block">**最新知识库文章**:

<div id="kb_recent">· [技术文章如何写作才能有较好的阅读体验](http://kb.cnblogs.com/page/562830/)
· [「代码家」的学习过程和学习经验分享](http://kb.cnblogs.com/page/554260/)
· [写给未来的程序媛](http://kb.cnblogs.com/page/556770/)
· [高质量的工程代码为什么难写](http://kb.cnblogs.com/page/558087/)
· [循序渐进地代码重构](http://kb.cnblogs.com/page/555750/)
</div>

» [更多知识库文章...](http://kb.cnblogs.com/)</div>

</div>

<div id="HistoryToday" class="c_ad_block">**历史上的今天:**
2008-01-28 [域名解析 A记录 MX记录 CNAME记录 TTL](http://www.cnblogs.com/goody9807/archive/2008/01/28/1055879.html)
2008-01-28 [Ajax 和 XML: 五种 Ajax 反模式](http://www.cnblogs.com/goody9807/archive/2008/01/28/1049602.html)
</div>

<script type="text/javascript">fixPostBody(); setTimeout(function () { incrementViewCount(cb_entryId); }, 50); deliverAdT2(); deliverAdC1(); deliverAdC2(); loadNewsAndKb(); loadBlogSignature(); LoadPostInfoBlock(cb_blogId, cb_entryId, cb_blogApp, cb_blogUserGuid); GetPrevNextPost(cb_entryId, cb_blogId, cb_entryCreatedDate); loadOptUnderPost(); GetHistoryToday(cb_blogId, cb_blogApp, cb_entryCreatedDate);</script></div>

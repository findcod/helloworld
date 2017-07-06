很久以前的提问，当时确实找了挺久的，后来找到原因了，现在补充上来：

Apache开启伪静态的总结
一般简单的几步：
1.去掉LoadModule rewrite_module modules/mod_rewrite.so前面的#
2。把AllowOverride none秘诀为AllowOverride All 
一般有两个地方，都改一下cgi-bin与www目录下，有些还是/目录下的，可以不改
3.保存后重启apache即可。
4.在项目部的根目录加上.htaccess文件，内容为：
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>

走完以上四步，伪静态就应该完成了，以Thinkphp为例，打开项目后，把/index.php去掉也应该是可以正常访问的。

如果以上都没有问题，再在配置文件中设置一下路由模式：'URL_MODEL' => '2'

一般走到这一步就没有问题了，我在wamp的环境下使用是没有问题的。

但当我把项目放到phpStudy的环境下时却不行了，找了很久没有发现原因。但其它电脑中的phpStudy又可以开启伪静态，于是把那个httpd.conf文件拿过来认真对比一下看有哪些的设置不同。

于是找到了UltraEdit这个工具可以很方便地对比两个文件的内容，又认真对比了一下，发现可以开启伪静态的那个文件多了一行：
Include conf/extra/httpd-php-sapi55.conf
于是，加上去，问题解决，伪静态在phpStudy的环境下也成功了。

但是新的问题又出现了，由于项目中需要用到soap的拓展，当加上以上那一行的时候，soap拓展就不能用了！！！！！

然后我又想到了对比工具，想到了wamp中的httpd.conf文件，打开来对比一下，发现wamp中也并没有加那一行，为什么能开启伪静态呢？

对比后发现phpStudy的httpd.conf中多出了fcgid这个东西，于是去搜索发现了这篇文章：http://www.admin10000.com/Doc... 终于找到真正的原因了，原来wamp是用Apache模块模式的，而phpStudy用的是FactCGI模式。

然后再搜索“apache在FastCGI模式下开启伪静态”，于是找到了这篇文章：
http://www.upupw.net/bug/n40.... 问题马上解决了，只要把.htaccess文件中的#RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L] 
改成RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
这样，phpStudy下也可以正常开启伪静态，并且不影响soap拓展的使用了。
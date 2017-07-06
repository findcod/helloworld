PHP在Apache中一共有三种工作方式：CGI模式、Apache模块DLL、FastCGI模式、

一、CGI模式

PHP 在 Apache 2中的 CGI模式。编辑Apache 配置文件httpd.conf 如下：

# PHP4 版写法
ScriptAlias /php/ "D:/php/"
AddType application/x-httpd-php  .php
Action application/x-httpd-php  "/php/php.exe"
# PHP5 版写法
ScriptAlias /php/ "D:/php/"
AddType application/x-httpd-php  .php
Action application/x-httpd-php  "/php/php-cgi.exe"

二、Apache模块模式

PHP 在 Apache 2中的 模块模式。编辑Apache 配置文件httpd.conf 如下：

# PHP4 版写法
LoadModule php4_module  "D:/php/php4apache2.dll"
AddType application/x-httpd-php .php
# 别忘了从 sapi 目录中把 php4apache2.dll 拷贝出来！
# PHP5 版写法
LoadModule php5_module  "D:/php/php5apache2.dll"
AddType application/x-httpd-php .php
PHPIniDir "D:/php"
# PHPIniDir 是用来指明php配置文件 php.ini 的路径

三、FastCGI模式

Apache下的FastCGI模块目前网上有两个版本 mod_fastcgi 和 mod_fcgid。 推荐用 mod_fcgid。

使用 mod_fcgid 配置fastCGI模式

下载的 mod_fcgid，将压缩包中的“mod_fcgid.so”文件复制到apache的“modules”目录。打开Apache的httpd.conf 文件，在最后加入如下配置：

LoadModule fcgid_module modules/mod_fcgid.so
<IfModule mod_fcgid.c>
    AddHandler fcgid-script .fcgi .php
    #php.ini的存放目录
    FcgidInitialEnv PHPRC "D:/PHP"
    # 设置PHP_FCGI_MAX_REQUESTS大于或等于FcgidMaxRequestsPerProcess，防止php-cgi进程在处理完所有请求前退出
    FcgidInitialEnv PHP_FCGI_MAX_REQUESTS 1000
    #php-cgi每个进程的最大请求数
    FcgidMaxRequestsPerProcess 1000
    #php-cgi最大的进程数
    FcgidMaxProcesses 5
    #最大执行时间
    FcgidIOTimeout 120
    FcgidIdleTimeout 120
    #php-cgi的路径
    FcgidWrapper "D:/PHP/php-cgi.exe" .php
    AddType application/x-httpd-php .php
</IfModule>
修改DocumentRoot 路径的配置为：

<Directory "D:/WWW">  
    Options Indexes FollowSymLinks ExecCGI
    Order allow,deny  
    Allow from all
    AllowOverride All
</Directory>
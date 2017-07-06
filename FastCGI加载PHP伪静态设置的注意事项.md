默认的

RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
规则在apache fastcgi模式下会导致No input file specified.

修改成

RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
就OK，地址正常重写。

我们都知道，使用伪静态相对来说，对搜索引擎比较友好，启用REWRITE的伪静态功能的时候，首页可以访问，而访问内页的时候，就提示：“No input file specified.”。

Wordpress及Typecho等程序默认的.htaccess里面的规则为例：

RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]
修改一下伪静态规则，如下：

RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php?/$1 [L]
在正则结果“$1”前面多加了一个“?”号，问题也就随之解决了。
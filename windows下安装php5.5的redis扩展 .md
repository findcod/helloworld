windows下安装php5.5的redis扩展  

2014-12-11 11:29:47|  分类： php |  标签：php  php_redis  xampp  windows  5.5   |举报|字号 订阅
    
  下载LOFTER我的照片书  |
windows下开发用的xampp集成的环境，想装个php-redis扩展，扩展的github地址：  https://github.com/nicolasff/phpredis
php_redis.dll下载地址：http://windows.php.net/downloads/pecl/snaps/redis/2.2.5/
windows下安装php5.5的redis扩展 - 范范snow - 我的小屋
看下自己phpinfo的信息
   windows下安装php5.5的redis扩展 - 范范snow - 我的小屋 
   windows下安装php5.5的redis扩展 - 范范snow - 我的小屋 
 就选择   ts-x86 的包下载，将下载解压后的 php_igbinary.dll和php_redis.dll放入php的ext目录下  
   然后修改php.ini，加入
; php-redis
extension=php_igbinary.dll
extension=php_redis.dll
重启apache，查看phpinfo就有redis扩展的信息了
   windows下安装php5.5的redis扩展 - 范范snow - 我的小屋
phpredis中文手册地址：http://www.cnblogs.com/ikodota/archive/2012/03/05/php_redis_cn.html

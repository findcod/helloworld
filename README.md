[SEA组件库](http://panxuepeng.github.io/seajslib/ "SEA组件库")

####在css中出现的相对路径，是以css文件所在路径为基准的，而js中的路径则是以导入此js的网页文件所在的位置为基准的

(转)CSS与JS中的相对路径引用简单介绍
javascript和css文件中采用相对路径，其基准路径是完全不同的。 

1.javascript引用资源(比如图片)相对路径是以宿主路径(被引用的网页比如你在首页index.php引用了某js文件，则index.php即为宿主)所处位置为基准。 

2.css引用资源(比如图片)相对路径是以.css文件所处位置为基准! 

大家在html中通常会导入一些外部的css、js文件，而其中一个比较容易被忽视的问题就是路径问题，有时候，我们在css、js中都有通过路径来引入一张图片的需求，当我们采用相对路径的时候，在css和js中引用图片的相对路径的基准是不一样的。在css中出现的相对路径，是以css文件所在路径为基准的，而js中的路径则是以导入此js的网页文件所在的位置为基准的。 

举个例子来说明这个问题。 

假如我们有如下文件目录树：（\是文件夹） 
–\site 
——\images 
———index_02.jpg 
——test.htm 

–\css 
——\css 
———-test.css 

–\js 
——\js 
———-test.js 

引用代码如下: 

test.css 


复制代码
代码如下:

#imgtest 
{ 
background-image:url(../../site/images/index_02.jpg); 
width:500px; 
height:50px; 
border:solid 1px red; 
} 
test.js 
function writeimg() 
{ 
document.write(“<img src=’images/index_02.jpg’ />”); 
} 

test.htm 


复制代码
代码如下:

<!DOCTYPE html PUBLIC “-//W3C//DTD XHTML 1.0 Transitional//EN” “http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd”> 
<html xmlns=”http://www.w3.org/1999/xhtml” > 
<head> 
<title>test</title> 
<script type=”text/javascript” src=”../js/js/test.js”></script> 
<link href=”../css/css/test.css” rel=”stylesheet” type=”text/css” /> 
</head> 
<body> 
<script type=”text/javascript”> 
writeimg(); 
</script> 
<div id=”imgtest”></div> 
</body> 
</html> 

从例子，我们注意到css引用的是css目录与index_02.jpg的关系，js引用的是test.htm目录与与index_02.jpg的关系。但一般时候我们的css和js文件多在不同的网页上引用，他们有不同的宿主环境，所以使用被调用的路径做基准路径是安全的，但使用调用者的路径作为基准路径会造成路径错误，无法加载。通常情况下，js不仅仅是一个html在于，特别对于动态网页而言，js中的路径引用应该采用绝对路径，可以通过定义一个全局变量如path来活动项目的真实路径（像jsp中就可以用request.getSession().getServletContext().getRealPath(“/”)），然后在每个路径上加上path（path+你的路径）从而取出路径错误的问题。

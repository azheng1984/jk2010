<?php
header('Cache-Control: max-age=0, private, must-revalidate');
header('Content-Type:text/html; charset=UTF-8');
?>
<html>
<head>
<title><?= $this['title'] ?></title>
<?php if (isset($this['css'])) {
    foreach ($this['css'] as $href) {?>
        <link href="<?= $href?>" rel="stylesheet" type="text/css"/>
<?php
    }
}?>
<style type="text/css">
body {
  color:#333;
  font:13px Arial,Helvetica,sans-serif;
  line-height:20px;
  margin:0;
	background:url(/assets/images/bg.png) #f1f1f1;
}
img {
  border:0;
}
#footer img {
	vertical-align:middle;
    width: 20px;
    height: 20px;
	margin-right: 5px;
}
a {
  color:#007aff;
  text-decoration:none;
}
a:hover,a:active {
  color:#007aff;
  border-color:#007aff;
	text-decoration:underline;
}
a:active {
/*
  color: #DD4B39;
  border-bottom:1px solid #DD4B39;
*/
}
h2 a:active {
/*
  color: #DD4B39;
  border-color:#DD4B39;
*/
}
div, form, ol, ul, h1, h2, h3, table {
  float:left;
}
h1, h2, h3 {
  font-weight:normal;
  margin:0;
  width: 100%;
}
ol, ul {
}
table {
  border-collapse:collapse;
  empty-cells:hide;
}
td {
  vertical-align:top;
}
#container {
  width:960px;
  margin:0 auto;
  float:none;
}
.page {
    background: #fff;
}
#logo {
	padding: 20px 5px;
	color: #fff;
	font-size: 26px;
	font-weight: bold;
}
#logo a:hover {
	text-decoration:none;
}
#nav {
	font-size: 16px;
	margin: 15px 20px;
	border-radius: 5px;
}
#nav div {
    padding: 5px 15px;
}
#nav .selected {
	background: #2C87C7;
	box-shadow: inset 0 1px 1px rgba(100,100,100,.1);
	border-radius: 5px;
}
#nav div.first {
	border-left: 0;
}
#header-wrapper {
	background-color: #5BB2EE;
	width: 100%;
	box-shadow: 0 0 4px rgba(0,0,0,.2);
}
#header {
  width:960px;
  margin:0 auto;
  float:none;
}
#header a {
	color: #fff;
	text-shadow: 0 1px 0 rgba(0,0,0,0.1);
}
#header #logo a {
	text-shadow: none;
}
#footer {
	width: 100%;
	margin: 20px 0;
	color: #888;
}
#footer span {
	color: #ccc;
}
h1 {
	font-size: 26px;
}
</style>
</head>
<body>
<div id="header-wrapper">
<div id="header">
<div id="logo"><a href="/cn">Hyperframework</a></div>
<div id="nav">
<?php 
    $topNav = null;
    if (isset($this['top_nav'])) {
        $topNav = $this['top_nav'];
    }
?>
    <div class="first<?php if ($topNav === 'home') {echo ' selected';}?>"><a href="/cn">首页</a></div>
    <div<?php if ($topNav === 'downloads') {echo ' class="selected"';}?>><a href="/cn/downloads">下载</a></div>
    <div<?php if ($topNav === 'docs') {echo ' class="selected"';}?>><a href="/cn/docs">文档</a></div>
</div>
</div>
</div>
<div id="container">
<?php $this->renderBlock('content'); ?>
<div id="footer"><a href="https://github.com/hyperframework/hyperframework"><img src="/assets/images/github.png" />Github</a> <span>|</span> Hyperframework 遵循 <a href="/cn/license">MIT 许可协议</a></div>
</div>
<?php if (isset($this['js'])) {
    foreach ($this['js'] as $href) {?>
        <script src="<?= $href?>"></script>
<?php
    }
}?>
<?php if (isset($this['js_code'])) {
        echo $this['js_code'];
    }
if ($_SERVER['SERVER_NAME'] !== 'localhost') {
?>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?67f46c3522cf55a1128e927dee1cb175";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
<?php } ?>
</body>
</html>

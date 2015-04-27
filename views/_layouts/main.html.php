<?php 
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
	margin: 20px;
	border-radius: 5px;
}
#nav div {
    padding: 0 15px;
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
    <div class="first"><a href="/cn">首页</a></div>
    <div><a href="/cn/downloads">下载</a></div>
    <div><a href="/cn/docs">文档</a></div>
</div>
</div>
</div>
<div id="container">

<?php $this->renderBlock('content'); ?>
<div id="footer"><a href="https://github.com/" target="blank">Github</a> <span>|</span> Hyperframework 遵循 <a href="/cn/license">MIT 许可协议</a></div>
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
?>
</body>
</html>

<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/github.css'];
$this['js'] = ['/highlight-8.4/highlight.pack.js'];
$this['js_code'] = '<script>hljs.initHighlightingOnLoad();</script>';
$this->setBlock('content', function() {?>
<style type="text/css">
#page {
    background: #fff;
	border-radius: 10px;
	width: 100%;
	box-shadow: 0 1px 1px rgba(100,100,100,.15);
	margin-top:10px;
	line-height: 1.6;
	font-size:16px;
}
#page .content {
	padding: 10px 20px;
}
#page div {
   clear:both;
	margin:10px 0;
}
h1, h2 {
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
	width: 920px;
}
h1 {
	margin-bottom: 10px;
}
h3 {
  font-size: 18px;
  line-height: 30px;
	margin: 5px 0;
}
</style>

<?php
if (isset($this['doc'])) {
    echo $this['doc'];
    return;
}
?>
<div id="page">
<div class="content">
<h1>文档</h1>
<h3><a href="/cn/manual">Hyperframework 手册</a></h3>
<h3><a href="/api/v1.0.0-alpha/index.html">API</a></h3>
</div></div>
<?php });?>


<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/manual.css'];
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
h1, h2, h3, h4 {
	width: 920px;
	clear:both;
	margin: 10px 0;
	font-weight:normal;
}
h1, h2 {
  padding-bottom: 5px;
  line-height: 30px;
  border-bottom: 1px solid #ddd;
}
h2 {
  font-size: 20px;
  line-height: 30px;
}
#page div, #page pre {
	clear:both;
}
</style>
<div id="page">
<div class="content">
<h1>下载</h1>
<h2>通过 Composer 下载</h2>
<div><strong></strong></div>
<div>1. 安装 Composer。安装方法参考 <a href="https://getcomposer.org/doc/">Composer 文档</a>。</div>
<div>2. 在项目根目录中新建 composer.json 文件，添加对 Hyperframework 的依赖：</div>
<pre><code class="json hljs">{
    "require": {
        "hyperframework/hyperframework": "*"
    }
}
</code></pre>
NOTE: 由于目前 Hyperframework 还没有发布正式版，所以需要加入 minimum-stability 字段来获取非正式版本，例如：
<pre><code class="json hljs">{
    "minimum-stability": "alpha",
    "require": {
        "hyperframework/hyperframework": "*"
    }
}
</code></pre>
<div>3. 在项目根目录中运行：</div>
<pre><code class="bash hljs">php composer.phar install</code></pre>
<div>Hyperframework 会被自动安装到项目根目录的 vendor/hyperframework/hyperframework 文件夹中。 </div>
<h2>通过 Github 下载</h2>
<div>项目地址：<a href="https://github.com/hyperframework/hyperframework">https://github.com/hyperframework/hyperframework</a></div>
</div></div>
<?php });?>

<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/github.css'];
$this['js'] = ['/highlight-8.4/highlight.pack.js'];
$this['js_code'] = '<script>hljs.initHighlightingOnLoad();</script>';
$this->setBlock('content', function() {?>
<h2><a href="/cn/manual">Hyperframework 手册</a></h2>
<?php 
if (isset($this['doc'])) {
    echo $this['doc'];
    return;
}
?>
<h3><a href="/cn/manual/copyright">版权声明</a></h3>
<h3><a href="/cn/manual/v1/web">Web 应用开发框架</a></h3>
<div>description</div>
<h3><a href="/cn/manual/v1/cli">命令行应用开发框架</a></h3>
<div>description</div>
<h3><a href="/cn/manual/v1/common">Common 模块</a></h3>
<div>description</div>
<h3><a href="/cn/manual/v1/db">Db 模块</a></h3>
<div>description</div>
<h3><a href="/cn/manual/v1/logging">Logging 模块</a></h3>
<div>description</div>
<?php });?>
<div></div>

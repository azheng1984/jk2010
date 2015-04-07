<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/github.css'];
$this['js'] = ['/highlight-8.4/highlight.pack.js'];
$this['js_code'] = '<script>hljs.initHighlightingOnLoad();</script>';
$this->setBlock('content', function() {?>
<?php 
if (isset($this['doc'])) {
    echo $this['doc'];
    return;
}
?>
<h2>文档</h2>
<h3><a href="/cn/manual/copyright">版权声明</a></h3>
<h3><a href="/cn/manual/web">Web 应用开发框架</a></h3>
<div><a href="/cn/manual/web/requirements">环境要求</a></div>
<div><a href="/cn/manual/web/getting_started">入门</a></div>
<div><a href="/cn/manual/web/configuration">配置</a></div>
<h3><a href="/cn/manual/cli">命令行应用开发框架</a></h3>
<div></div>
<div>安装</div>
<div>配置</div>
<h3><a href="/cn/manual/common">平台无关的支持模块</a></h3>
<div>安装</div>
<div>配置</div>
<h3><a href="/cn/manual/db">数据库访问模块</a></h3>
<div>安装</div>
<h3><a href="/cn/manual/logging">日志模块</a></h3>
<div>安装</div>
<?php });?>
<div></div>

<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/default.css'];
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
<h3><a href="/cn/docs/copyright">版权声明</a></h3>
<h3><a href="/cn/docs/installation">安装</a></h3>
<h3>Web</h3>
<div>Web 应用开发框架</div>
<div><a href="/cn/docs/web/getting_started">入门</a></div>
<div>路由</div>
<div>控制器</div>
<div>视图</div>
<div>错误处理</div>
<div>Csrf 攻击防护</div>
<div>调试</div>
<div>配置</div>
<div>API</div>
<h3>Cli</h3>
<div>命令行应用开发框架</div>
<div>入门</div>
<div>命令选项</div>
<div>命令参数</div>
<div>帮助</div>
<div>配置</div>
<div>API</div>
<h3>Common</h3>
<div>平台无关的支持模块</div>
<div>入门</div>
<div>配置</div>
<div>API</div>
<h3>Db</h3>
<div>数据库访问模块</div>
<div>入门</div>
<div>API</div>
<h3>Logging</h3>
<div>日志模块</div>
<div>入门</div>
<div>API</div>
<?php });?>
<div></div>

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
<h1>文档</h1>
<h2><a href="/cn/manual">Hyperframework 手册 (v1)</a></h2>
<h2><a href="/api/v1.0.0-alpha/index.html">API</a> (v1.0.0 alpha)</h2>
<?php });?>
<div></div>

<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/github.css'];
$this['js'] = ['/highlight-8.4/highlight.pack.js'];
$this['js_code'] = '<script>hljs.initHighlightingOnLoad();</script>';
$this->setBlock('content', function() {
    if ($this['nav'] === null) {
      echo '<h2>Hyperframework 手册</h2>';
    } else {
?>
<h2><a href="/cn/manual">Hyperframework 手册</a></h2>
<?php 
        if ($this['nav'] !== 'index') {
            echo '<div><a href="/cn/manual/', $this['nav'][0], '">', $this['nav'][1], '</a></div>';
        }
    }
    echo $this['doc'];
});?>
<div></div>

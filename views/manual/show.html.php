<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/googlecode.css'];
$this['js'] = ['/highlight-8.4/highlight.pack.js'];
$this['js_code'] = '<script>hljs.initHighlightingOnLoad();</script>';

$this->setBlock('content', function() {?>
<style type="text/css">
#manual {
	    	background: #fff;
    	border-radius: 10px;
    	width: 100%;
    	box-shadow: 0 1px 1px rgba(100,100,100,.15);
	margin-top:10px;
}
#manual .content {
	padding: 10px;
}
</style>
<div id="manual">
<div class="content">
<?php
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
    ?>
</div> </div>
<?php
});?>

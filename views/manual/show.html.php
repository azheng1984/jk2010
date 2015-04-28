<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this['css'] = ['/highlight-8.4/styles/manual.css'];
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
	line-height: 1.6;
	font-size:16px;
}
.hljs, .nohighlight {
	background:#f7f7f7;
	font-size: 14px;
}
.nohighlight {
	display: block;
	padding: 0.5em;
}
#manual .content {
	padding: 10px 20px;
}
#breadcrumb {
	margin:10px 0 0 5px;
	color: #aaa;
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
h3 {
  font-size: 18px;
  line-height: 30px;
  margin: 5px 0;
}
table {
	margin-bottom: 16px;
}
pre, table,p,li {
	clear:both;
}
table th {
	padding: 6px 13px;
  border: 1px solid #ddd;
}
table tr {
  background-color: #fff;
  border-top: 1px solid #ccc;
}
tr:nth-child(2n) {
  background-color: #f8f8f8;
}
table td {
  padding: 6px 13px;
  border: 1px solid #ddd;
}
</style>
<?php 
if ($this['nav'] !== null) {
    ?>
<div id ="breadcrumb"><a href="/cn/manual">Hyperframework 手册</a><?php
        if ($this['nav'] !== 'index') {
            echo '&nbsp; › &nbsp;<a href="/cn/manual/', $this['nav'][0], '">', $this['nav'][1], '</a>';
        }?>
        </div>
        <?php
    }
?>
<div id="manual">
<div class="content">
<?php
    if ($this['nav'] === null) {
        echo '<h1>Hyperframework 手册</h1>';
    }
    echo $this['doc'];
?>
 </div>
 </div>
<?php
});?>

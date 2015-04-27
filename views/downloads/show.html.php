<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
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
}h3 {
  font-size: 1.5em;
  line-height: 1.43;
}
</style>
<div id="page">
<div class="content">
<h1>下载</h1>
</div></div>
<?php });?>

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
}h1, h2 {
  padding-bottom: 0.3em;
  font-size: 2.25em;
  line-height: 1.2;
  border-bottom: 1px solid #eee;
	width: 920px;
}
</style>
<div id="page">
<div class="content">
<h1>The MIT License</h1>
<div>
<div>Copyright © 2015 hyperframework.com</div>

<div>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</div>

<div>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</div>

<div>THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</div>
</div></div>
</div>
<?php });?>

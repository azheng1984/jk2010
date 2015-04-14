<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this->setBlock('content', function() {?>
<h2>Hyperframework 是一个简单、专业的 PHP 框架。</h2>
<h2>Hyperframework 1.0.0 alpha 发布了！</h2>
<div>此次发布包含 5 个模块</div>
<h3>1. Web</h3>
<div>Web 应用开发框架</div>
<h3>2. Cli</h3>
<div>命令行应用开发框架</div>
<h3>3. Common</h3>
<div>平台无关的支持模块</div>
<h3>4. Db</h3>
<div>数据库访问模块</div>
<h3>5. Logging</h3>
<div>日志模块</div>
<br />
<div></div>
<div>详细信息请查看<a href="/cn/docs">文档</a>。</div>
<?php });?>

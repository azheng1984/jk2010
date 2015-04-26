<?php
/* @var $this Hyperframework\Web\ViewKernel */
$this->setLayout('_layouts/main.html.php');
$this->setBlock('content', function() {
    ?>
    <style type="text/css">
    #home h3 {
    	color: #333;
    	font-size: 16px;
    	border-top: 1px solid #f1f1f1;
    	padding: 10px 0;
    	margin-top: 10px;
    	color: #8fb070;
    }
    #home div {
    	color: #666;
    }
    h1, #modules, #features {
    	background: #fff;
    	border-radius: 10px;
    	width: 100%;
    }
    h1 {
    	text-align: center;
    	background: #ddd;
    	margin-top: 20px;
}
    h1 span{
    	line-height: 50px;
    	padding: 10px;
    }
    #modules .content, #features .content {
    	padding: 10px;
    }
    h2 {
    	color: #777;
    	padding: 20px 5px 10px 5px;
    }
    #home h3.first {
    	border-top: 0;
    	padding-top: 0;
    	margin-top:0;
    }
    #home h3 span {
    	color: #777;
    	font-size: 13px;
    }
    #home h3 img {
    	vertical-align: middle;
    	margin-right: 10px;
    }
    </style>
<div id="home">
<h1><span>简单、专业的 PHP 框架。</span></h1>
<h2>模块</h2>
<div id="modules"><div class="content">
<h3 class="first"><img alt="Web 应用开发框架" src="/assets/images/web.png"/>Web<span> - Web 应用开发框架</span></h3>
<h3><img alt="命令行应用开发框架" src="/assets/images/cli.png"/>Cli<span> - 命令行应用开发框架</span></h3>
<h3><img alt="数据库访问模块" src="/assets/images/db.png"/>Db<span> - 数据库访问模块</span></h3>
<h3><img alt="日志模块" src="/assets/images/logging.png"/>Logging<span> - 日志模块</span></h3>
<h3><img alt="平台无关的支持模块" src="/assets/images/common.png"/>Common<span> - 平台无关的支持模块</span></h3>
</div></div>
<h2>特性</h2>
<div id="features"><div class="content">
<h3 class="first">简单</h3>
<div>使用简单，并提供手册和 API 文档。</div>
<h3>专业</h3>
<div>统一的面向对象编码风格，完善的单元测试覆盖。</div>
<h3>模块化</h3>
<div>框架由可单独使用的模块组成，并基于 Composer，无缝整合社区生态系统。</div>
<h3>MVC</h3>
<div>Web 应用架构符合 MVC 模式。</div>
<h3>RESTful</h3>
<div>支持 RESTful 的网站和 API 应用。</div>
<h3>支持过滤器</h3>
<div>控制器支持前置、后置和环绕过滤器。</div>
<h3>优化的错误页面</h3>
<div>通过内部和外部代码分离，错误信息和输出数据分离，辅助开发者更快定位错误。</div>
<h3>支持视图模板</h3>
<div>提供基于原生 PHP 的视图模板系统，并支持整合其他视图模板系统。</div>
<h3>规范化的命令行交互模式</h3>
<div>命令行应用符合 GNU 命令行规范。</div>
<h3>支持 Active Record</h3>
<div>支持 Active Record 模式，用于构建领域模型。</div>
<h3>支持数据库操作剖析</h3>
<div>支持记录数据库操作，用于排错和性能调优。</div>
<h3>支持多数据库应用</h3>
<div>支持同时连接多个数据库，并提供完善的连接管理功能。</div>
<h3>自动化事务处理</h3>
<div>提供了事务的自动提交和回滚功能。</div>
<h3>可扩展的日志系统</h3>
<div>内置的日志系统高度可扩展，并支持整合其他日志系统。</div>
<h3>支持环境配置</h3>
<div>支持为不同的部署环境定制配置。</div>
</div>
</div></div>
<?php });?>

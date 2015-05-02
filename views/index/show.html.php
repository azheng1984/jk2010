<?php
/* @var $this Hyperframework\Web\View */
$this->setLayout('_layouts/main.html.php');
$this->setBlock('content', function() {
    ?>
    <style type="text/css">
    #home h3 {
    	color: #333;
    	font-size: 16px;
    	border-top: 1px solid #f1f1f1;
    	padding: 10px 0 0 0;
    	margin-top: 10px;
/*     	color: #8fb070; */
/*     	color: #072; */
    }
    #home div {
    	color: #555;
    }
    h1, #modules, #features {
    	background: #fff;
    	border-radius: 10px;
    	width: 100%;
    	box-shadow: 0 1px 1px rgba(100,100,100,.15);
    }
    h1 {
    	font-size:26px;
    	text-align: center;
    	background: #E0E8EF;
    	color: #666;
    	margin-top: 20px;
    	box-shadow: inset 0 1px 1px rgba(100,100,100,.1);
}
    h1 span{
    	line-height: 50px;
    	padding: 10px;
    }
    #modules .content, #features .content {
    	padding: 10px;
    }
    #modules h3 {
    	font-size: 20px;
    	padding-top: 15px;
    }
    h2 {
    	font-size:22px;
    	color: #777;
    	padding: 20px 5px 10px 5px;
    }
    #home h3.first {
    	border-top: 0;
    	padding-top: 5px;
    	margin-top:0;
    }
    #home h3 span {
    	color: #777;
    	font-size: 13px;
    	background: #EFF2F4;
        padding: 3px 6px;
    	margin-left: 6px;
        border-radius: 3px;
    	box-shadow: inset 0 1px 1px rgba(100,100,100,.1);
    }
    #home h3 img {
    	vertical-align: middle;
    	margin-right: 10px;
    	margin-left: 5px;
    }
    #home .content li {
    	line-height: 25px;
    	font-size: 14px;
    }
    </style>
<div id="home">
<h1><span>简单、专业的 PHP 框架。</span></h1>
<div> &nbsp;</div>
<div id="modules"><div class="content">
<h3 class="first"><img alt="Web 应用开发框架" src="/assets/images/web.png"/>Web <span>Web 应用开发框架</span></h3>
<ul><li>应用架构符合 MVC 模式。</li><li>支持 RESTful 的网站和 API 应用。</li><li>控制器支持前置、后置和环绕过滤器。</li><li>优化错误信息，更快定位错误。</li><li>提供基于原生 PHP 的视图模板系统，并支持整合其他视图模板系统。</li></ul>
<h3><img alt="命令行应用开发框架" src="/assets/images/cli.png"/>Cli <span>命令行应用开发框架</span></h3>
<ul>
<li>规范化的命令行交互模式，符合 GNU 命令行规范。</li>
<li>支持多命令应用。</li>
<li>根据配置，生成帮助信息。</li>
</ul>
<h3><img alt="数据库访问模块" src="/assets/images/db.png"/>Db <span>数据库访问模块</span></h3>
<ul>
<li>支持 Active Record 模式，用于构建领域模型。</li>
<li>支持数据库操作剖析，用于排错和性能调优。</li>
<li>支持事务的自动提交和回滚。</li>
</ul>
<h3><img alt="日志模块" src="/assets/images/logging.png"/>Logging <span>日志模块</span></h3>
<ul>
<li>可扩展的日志系统。</li>
<li>支持日志信息延迟生成。</li>
</ul>
<h3><img alt="平台无关的支持模块" src="/assets/images/common.png"/>Common <span>平台无关的支持模块</span></h3>
<ul>
<li>包含配置管理，错误处理，单复数转换等通用功能。</li>
<li>支持为不同的部署环境定制配置。</li>
</ul>
</div></div>
</div>
<?php });?>
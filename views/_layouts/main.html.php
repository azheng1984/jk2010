<html>
<head>
<title><?= $this['title'] ?></title>
<?php if (isset($this['css'])) {
    foreach ($this['css'] as $href) {?>
        <link href="<?= $href?>" rel="stylesheet" type="text/css"/>
<?php
    }
}?>

</head>
<body>
<div>
    <h1>Hyperframework</h1>
</div>
<div>
    <a href="/cn">首页</a>
    <a href="/cn/docs">文档</a>
    <a href="/cn/blog">日志</a>
</div>
<?php $this->renderBlock('content'); ?>
<div><a href="https://github.com/" target="blank">Github</a> | Hyperframework 遵循 <a href="/cn/license">MIT 许可协议</a></div>
<?php if (isset($this['js'])) {
    foreach ($this['js'] as $href) {?>
        <script src="<?= $href?>"></script>
<?php
    }
}?>
<?php if (isset($this['js_code'])) {
        echo $this['js_code'];
    }
?>
</body>
</html>

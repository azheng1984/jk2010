<?php
$this->setLayout('_layouts/main.html.php');
$this['title'] = '页面不存在 - Hyperframework';
$this->setBlock('content', function() {
    echo '<h1 style="padding: 50px 10px;">页面不存在。</h1>';
});

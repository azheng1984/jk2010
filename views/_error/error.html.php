<?php
$this->setLayout('_layouts/main.html.php');
$this['title'] = '出错了 - Hyperframework';
$this->setBlock('content', function() {
    echo '<h1 style="padding: 50px 10px;">出错了。</h1>';
});

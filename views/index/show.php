<?php
$this->extend('index/_layout');
$this(function() {
    $this['title'] = 'xxx';
    $this['description'] = 'xxx';
});
$this->setBlock('content', function() {
});
$this->setBlock('menu', function() {
    $this->load('index/_hello');
});
$this->setBlock('left', function() { ?>
left
<?php });
$this->setBlock('footer', function() { ?>
footer
<?php });

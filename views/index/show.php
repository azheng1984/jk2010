<?php
$this->setLayout('index/_layout');
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
<span>
left
</span>
<?php });
$this->setBlock('footer', function() { ?>
footer xx
<?php });
//$this->setBlock('hi', function() {
//    var_dump('block hi');
//});

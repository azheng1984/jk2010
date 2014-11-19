<?php
use Hyperframework\Web\Html\FormHelper;
$this->setLayout('index/_layout');
$this(function() {
    $this['title'] = 'xxx';
    $this['description'] = 'xxx';
});
$this->setBlock('content', function() {
    $f = new FormHelper(['hi' => 'hello world']);
    $f->begin();
    $f->renderTextBox(['name' => 'hi']);
    $f->end();
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

<?php
use Hyperframework\Web\Html\FormHelper;
$this->setLayout('index/_layout');
$this(function() {
    $this['title'] = 'xxx';
    $this['description'] = 'xxx';
});
$this->setBlock('content', function() {
    $f = new FormHelper(['hi' => '3']);
    $f->begin(['method' => 'delete']);
    $f->renderTextField(['name' => 'hi']);
    $f->renderTextArea(['name' => 'hi']);
    $f->renderSelect(['name' => 'hi', ':options' => ['1', 'hi', '2', '3']]);
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

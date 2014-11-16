<?php
var_dump($this->pathStack);
$this(function() {
    $this['title'] = 'xxx';
    $this['description'] = 'xxx';
    $this->extend('todo.php');
});
?>
<?php $this->setBlock('content', function() { ?>
home
<?php });
$this->setBlock('menu', function() { ?>
menu
<?php });
$this->setBlock('left', function() { ?>
left
<?php });
$this->setBlock('footer', function() { ?>
footer
<?php });

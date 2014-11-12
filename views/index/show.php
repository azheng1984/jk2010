<?php 
echo '>';
var_dump($path);
$this(function() {
    $this['title'] = 'xxx';
    $var = 'xx';
    $this['description'] = 'xxx';
    $this->setLayout('todo.php');
});
?>
<?php $this->setBlock('content', function() { ?>
home
<?php });

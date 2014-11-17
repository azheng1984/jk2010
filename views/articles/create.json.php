<?php
$this->extend('/_layout/app');
$this->usePath('_layout/app', 'app');
$this->usePath('xxxx');
$this->usePath('xxxx/list');
$this->setBlock('hello', function() {
});
$this(function() {
});
$this['article'];
return function() {?>
<div>hello world</div>
    <div><?= $this['title'] ?></div>
<?php };
echo '<xml>';

<?php
$this->extend('/_layout/app');
$this->usePath('_layout/app', 'app');
$this->usePath('xxxx');
$this->usePath('root://xxxx/list');

$this->usePath('vendor://lib/base');
$this->load('vendor://main/article/list');
$this->load('main/article/list');
$this->load('root://main/article/list');

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

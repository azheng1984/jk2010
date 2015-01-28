<?php
/* @var $this Hyperframework\Common\View */

$this->setBlock('hi', function() {
    echo 'hi';
});
$this->renderBlock('');
$this->renderBlock('hi');
return function() {
};
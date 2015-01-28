<?php
/* @var $this Hyperframework\Web\View */

$this->setBlock('hi', function() {
    echo 'hi';
});
$this->renderBlock('');
$this->renderBlock('hi');
return function() {
};
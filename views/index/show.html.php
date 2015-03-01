<?php
/* @var $this Hyperframework\Web\View */
$this->setBlock('hi', function() {
    echo 'hi from view';
});
$this->renderBlock('hi');

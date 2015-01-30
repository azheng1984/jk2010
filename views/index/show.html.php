<?php
/* @var $this Hyperframework\Web\View */
$this->setBlock('hi', function() {
    echo 'hi';
});
$this->renderBlock('hi');

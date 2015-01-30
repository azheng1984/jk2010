<?php
/* @var $this Hyperframework\Web\View */
df;
$this->setBlock('hi', function() {
    echo 'hi';
});
$this->renderBlock('hi');

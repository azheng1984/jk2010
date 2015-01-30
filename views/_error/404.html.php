<?php
$this->setLayout('_layouts/main');
$this->setBlock('body', function() {
    $this->render('_error/share');
    echo "i'm 404 view";
});

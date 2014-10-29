<?php
namespace Youxuanji\Main\Controllers;

class ArticlesController extends Controller {
    public function __construct($app) {
    }

//    public function onExecuting() {
//    }
//
//    public function onExecuted() {
//    }

    public function doShowAction() {
    }

    public function doNewAction() {
    }

    public function doDeleteAction() {
    }

    public function doListAction() {
    }
}

namespace Youxuanji\View;

$this->setLayout('list');

$this->setBlock('xxx', function() {
});

$this->setBlock('');
$this->setBlock('xxx');

$this->setBlockNamespace();

$this->renderBlock('xxx');

$this->setLayout('xxx');
$this->renderBlock('xxx', function() {
});

$this->renderBlock('xx', function() {
});

$this->setBlock('xxx', function() {
    $this->renderBlock('rename');
});

$this->renameBlock('xxx', 'xxxxx');

$this->setBlock('xxx', function() {
});

$this->extend();

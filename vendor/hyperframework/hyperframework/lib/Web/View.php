<?php
namespace Hyperframework\Web;

class View extends ViewTemplate {
    public function __construct($viewModel = null) {
        $loadFileFunction = function() {
            require $this->getFilePath();
        };
        parent::__construct($loadFileFunction, $viewModel);
    }
}

<?php
namespace Hyperframework\Web;

class View extends ViewTemplate {
    /**
     * @param array $viewModel
     */
    public function __construct(array $viewModel = null) {
        $loadFileFunction = function() {
            require $this->getFilePath();
        };
        parent::__construct($loadFileFunction, $viewModel);
    }
}

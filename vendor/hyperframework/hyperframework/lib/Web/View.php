<?php
namespace Hyperframework\Web;

class View extends AbstractView {
    /**
     * @param array $viewModel
     */
    public function __construct(array $viewModel = null) {
        $loadFileFunction = function() {
            require $this->getFile();
        };
        parent::__construct($loadFileFunction, $viewModel);
    }
}

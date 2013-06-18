<?php
namespace Hyperframework\Web;

class ApiApplication extends Application {
    private $viewConfig;

    public function __construct($viewConfig) {

    }

    protected function executeView() {
        if ($this->isViewEnabled === false) {
            return;
        }
        $config = null;
        $processor = new ViewProcessor;
        $processor->run($config);        
    }

    protected function dispatch() {
        $_GLOBALS['ACTION_RESULT'] = $this->executeAction();
        $this->executeView();
    }
}

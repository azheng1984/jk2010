<?php
namespace Hyperframework\Web;

class Application {
    private $isViewEnabled = true;
    private $actionResult;

    public function run($info) {
        $this->executeAction($info);
        $this->executeView($info);
    }

    public function enableView() {
        $this->isViewEnabled = true;
    }

    public function disableView() {
        $this->isViewEnabled = false;
    }

    public function getActionResult() {
        return $this->actionResult;
    }

    protected function executeAction($info) {
        $actionInfo = null;
        if (isset($info['Action'])) {
            $actionInfo = $info['Action'];
        }
        $processor = new ActionProcessor;
        $this->actionResult = $processor->run($actionInfo);
    }

    protected function executeView($info) {
        if (isset($info['View']) && $this->isViewEnabled) {
            $processor = new ViewProcessor;
            $processor->run($info['View']);
        }
    }
}

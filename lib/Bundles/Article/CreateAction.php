<?php

class CreateAction extends Action {
    public function execute() {
        if ($this->hasParam['id']) {
            $this->redirect();
            $this->quit();
        }
    }
}

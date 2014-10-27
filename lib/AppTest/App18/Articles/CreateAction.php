<?php

class CreateAction extends Action {
    public function run() {
        if ($this->hasParam['id']) {
            $this->redirect();
            $this->quit();
        }
    }
}

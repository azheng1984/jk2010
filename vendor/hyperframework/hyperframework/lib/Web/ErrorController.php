<?php

class ErrorController extends Controller {
    public function __construct($exception) {
    }

    public function getException() {
    }

    public function doErrorAction() {
        $this->setView('_error/error');
    }
}

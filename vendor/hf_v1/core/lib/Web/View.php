<?php

class View {
    public function render($actionResult) {
    }

    public function __invoke($function) {
        $function();
    }
}

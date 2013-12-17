<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $jsLinks = array();
    private $cssLinks = array();

    protected function addJsLink($path, $isRelative = false) {
    }

    protected function addCssLink($href) {
    }

    protected function renderJsLinks() {
        foreach ($this->jsLinks as $href) {
           $this->renderjsLink($href); 
        }
    }

    abstract protected function renderBody();
}

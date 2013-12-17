<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $jsLinks = array();
    private $cssLinks = array();

    public function render() {
        echo '<html><head></head><body></body></html>';
    }

    abstract protected function renderBody();

    abstract protected function renderBody();

    protected function addJsLink($path, $isRelative = false) {
    }

    protected function addCssLink($path, $media = null, $isRelative = false) {
    }

    protected function renderJsLinks() {
        foreach ($this->jsLinks as $link) {
            JsLink::render($link[0], $link[1]);
        }
    }

    protected function renderCssLinks() {
        foreach ($this->cssLinks as $link) {
            CssLink::render($link[0], $link[1], $link[2]);
        }
    }
}

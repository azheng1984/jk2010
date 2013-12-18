<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $language = null;
    private $jsLinks = array();
    private $cssLinks = array();

    public function render() {
        echo '<!DOCTYPE html><html><head>',
            $this->renderHead(),
            '</head><body>',
            $this->renderBody(),
            '</body></html>';
    }

    public function setLanguage($value) {
        $this->language = $value;
    }

    abstract protected function renderHead();

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

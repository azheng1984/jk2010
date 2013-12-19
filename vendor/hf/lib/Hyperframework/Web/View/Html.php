<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $language;
    private $isJsPreloadEnabled;
    private $isCssPreloadEnabled;
    private $jsManifest = array();
    private $cssManifest = array();

    public function render() {
        echo '<!DOCTYPE html><html><head>',
            $this->renderHead(),
            '</head><body>',
            $this->renderBody(),
            '</body></html>';
    }

//    public function setLanguage($value) {
//        $this->language = $value;
//    }

    abstract protected function renderHead();

    abstract protected function renderBody();

    protected function addJs($path, $isRelative = false) {
        $this->jsManifest[] =
            array('path' => $path, 'is_relative' => $isRelative);
    }

    protected function isJsPreloadEnabled() {
        if ($this->isJsPreloadEnabled === null) {
            $this->isJsPreloadEnabled =
                Config::get(__CLASS__ . '\JsPreloadEnabled') === true;
        }
        return $this->isJsPreloadEnabled;
    }

    protected function addCss($path, $media = null, $isRelative = false) {
    }

    protected function renderJsLinks() {
        foreach ($this->jsManifest as $js) {
            JsLink::render($js['url'], $js['is_relative']);
        }
    }

    protected function renderPreloadedJsLink() {
        if (static::isJsPreloadEnabled()) {
            JsLink::render(Config::get(
                __CLASS__ . '\JsPreloadFileName', array('default' => 'app.js')
            ));
        }
    }

    protected function renderJsLink() {
        $this->jsManifest->getAll();
    }

    protected function renderCssLinks() {
        foreach ($this->cssManifest as $css) {
            CssLink::render($css[0], $css[1], $css[2]);
        }
    }
}

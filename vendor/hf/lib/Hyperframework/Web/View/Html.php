<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $language;

    public function render() {
        echo '<!DOCTYPE html><html><head>',
            $this->renderHead(),
            '</head><body>',
            $this->renderBody(),
            '</body></html>';
    }

    public function renderPreloadCss()
    {
        if (AssetPreloader::hasCss()) {
            AssetPreloader::renderCssLink();
            return;
        }
        $this->renderPreloadCssLinks();
    }

    protected function renderPreloadCssLinks() {}

    abstract protected function renderHead();

    abstract protected function renderBody();
}

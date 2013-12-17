<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $jsLinks = array();
    private $cssLinks = array();

    protected function addJsLink($href) {
    }

    protected function addCssLink($href) {
    }

    protected function renderJsLink($href) {
        echo '<script type="text/javascript" src="' , $source , '"></script>';
    }

    protected function renderJsLinks() {
        foreach ($this->jsLinks as $href) {
           $this->renderjsLink($href); 
        }
    }

    abstract protected function renderBody();

    public static function renderCssLink($href, $media = null) {
        echo '<link rel="stylesheet" type="text/css" href="', $href, '"';
        if ($media !== null) {
            echo ' media="screen"';
        }
        echo '/>';
    }

    private static function renderShortcutIconLink($href) {
        echo '<link rel="shortcut icon" href="', $href, '">';
    }
}

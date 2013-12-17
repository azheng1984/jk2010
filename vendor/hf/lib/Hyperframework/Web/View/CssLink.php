<?php
namespace Hyperframework\Web\View;

class CssLink {
    public static function render(
        $path, $media = null, $isRelativePath = false
    ) {
        $options = array('is_relative' => $isRelativePath);
        if ($isRelativePath === false) {
            $options['default_root_path'] = \Hyperframework\Config::get(
                __CLASS__ . '\RootPath', array('default' => '/asset/css')
            );
        }
        echo '<link rel="stylesheet" type="text/css" href="', $href, '"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo '/>';
    }
}

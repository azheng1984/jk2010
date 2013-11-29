<?php
namespace Hyperframework\Web;

class Asset {
    public static function renderJsLink($path) {
        //check path namespace
        echo '<script src="' . $path . '" ></script>';
    }
}

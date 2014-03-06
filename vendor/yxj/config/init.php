<?php
namespace Yxj;

const HYPERFRMEWORK_PATH = '/home/azheng/daoxila_www/vendor/hf/core/lib';

function get_init_config() {
    return array(
        ['Hyperframework\ClassLoader\CacheEnabled', false],
        ['Hyperframework\Web\PathInfo\CacheEnabled', false],
        ['Hyperframework\Web\View\Asset\CacheEnabled', false],
    );
}

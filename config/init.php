<?php
namespace Hyperframework\Blog;

return array(
    '[hyperframework]',
    'app_root_namespace' => __NAMESPACE__,
//    'web.debug' => true,
    'asset.concatenate_manifest' => false,
    'asset.enable_versioning' => true,
    'asset.enable_proxy' => true,
    'path_info.enable_cache' => false,
//    'class_loader.root_path' => 'phar://' . ROOT_PATH . '/tmp/cache/lib.phar',
    'use_composer_autoloader' => true,
    'path_info.enable_cache' => false,
//    'web.error_handler.exit_level' => 'NOTICE',
//    'class_loader.enable_zero_folder' => true,
    'db.profiler.enable' => true,
    'logger.log_level' => 'DEBUG',
    'error_handler.enable_logger' => true,
//    'log_handler.path' => 'php://output',
    /////////////////////////
    '[hyperframework.blog]',
    'xx' => 'xxx'
);

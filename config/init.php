<?php
namespace Hyperframework\Blog;

return array(
    '[hyperframework]',
    'app_root_namespace' => __NAMESPACE__,
    'error_handler.debug' => true,
//  'web.router' => __NAMESPACE__ . '\Router',
    'web.debugger.max_output_content_size' => '' ,
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
    'logger.handler_class' => 'xx',
    'app_root_namespace' => __NAMESPACE__,
    '[hyperframework.error_handler]',
    'logger.enable' => false,
    'logger.log_stack_trace' => true,

//    'log_handler.path' => 'php://output',
    /////////////////////////
    '[hyperframework.blog]',
    'xx' => 'xxx'
);

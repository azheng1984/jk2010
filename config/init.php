<?php
namespace Hyperframework\Blog;
ini_set('display_errors', true);
ini_set('html_errors', true);

return array(
    '[hyperframework]',
//    'logging.log_path' => '/x/x/x.log',
    'app_root_namespace' => __NAMESPACE__,
    'error_handler.debug' => true,
//  'web.router' => __NAMESPACE__ . '\Router',
    'web.debugger.max_output_content_size' => 'unlimited',
    'asset.concatenate_manifest' => false,
    'asset.enable_versioning' => true,
    'asset.enable_proxy' => true,
    'path_info.enable_cache' => false,
//  'class_loader.root_path' => 'phar://' . ROOT_PATH . '/tmp/cache/lib.phar',
    'use_composer_autoloader' => true,
    'path_info.enable_cache' => false,
//  'web.error_handler.exit_level' => 'NOTICE',
//  'class_loader.enable_zero_folder' => true,
    'db.profiler.enable' => true,
    'logging.log_level' => 'DEBUG',
    'logging.handler_class' => 'xx',
    'app_root_namespace' => __NAMESPACE__,
    '[hyperframework.error_handler]',
    'logger.enable' => true,
//  'logger.log_stack_trace' => true,
//  'log_handler.path' => 'php://output',
    /////////////////////////
    '[hyperframework.blog]',
    'xx' => 'xxx'
);

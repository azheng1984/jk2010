<?php
return array(
  'description' => 'Hyperframework CLI Tool - version 0.1',
  'sub' => array(
    'build' => array(
      'class' => 'BuildCommand',
      'description' => 'Build application',
    ),
    'new' => array(
      'description' => 'Create scaffold',
      'class' => 'NewCommand',
      'option' => array(
        'class_loader_path' => array(
          'description' => 'Set class_loader_path instead of default',
          'short' => 'p',
          'class' => 'ClassLoaderPath',
        ),
      ),
    ),
  ),
);
<?php
return array (
  'option' => array (
    'version' => array (
      'short' => 'v',
      'expansion' => array ('help', 'version'),
      'description' => 'print version infomation',
    ),
    'help' => array (
      'short' => array ('h', '?'),
      'expansion' => array ('help'),
      'description' => 'show help',
    ),
  ),
  'sub' => array (
    'make' => array (
      'option' => array (
        'preview' => array (
          'short' => 'p',
          'description' => 'output execute info only, do not do real action',
        ),
        'quite' => array (
          'short' => 'q',
          'description' => 'do not output execute info',
        ),
      ),
      'description' => '',
    ),
    'new' => array (
      'sub' => array (
        'web' => array (
          'class' => 'NewWebCommand',
        ),
        'cli' => array (
          'class' => 'NewCliCommand',
        ),
      ),
    ),
    'help' => 'HelpCommand',
  ),
  'default_sub' => array('help'),
);
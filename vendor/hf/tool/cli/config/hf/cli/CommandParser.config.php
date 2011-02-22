<?php
return array (
  'option' => array (
    'version' => array (
      'short' => 'v',
      'expansion' => array ('help', 'version'),
      'description' => '',
    ),
    'help' => array (
      'short' => array ('h', '?'),
      'description' => '你好',
    ),
  ),
  'sub' => array (
    'make' => array (
      'class' => 'MakeCommand',
      'option' => array (
        'message' => array (
          'short' => 'm',
          'class' => 'MessageOption',
          'infinite_argument',
          'description' => '',
        ),
        'pagination',
      ),
      'infinite_argument',
      'description' => '',
    ),
    'new' => array (
      'sub' => array (
        'web' => 'NewWebCommand',
        'cli' => array (
          'class' => 'NewCliCommand'
        ),
      ),
    ),
    'help' => 'HelpCommand',
  ),
  'default' => array('help'),
);
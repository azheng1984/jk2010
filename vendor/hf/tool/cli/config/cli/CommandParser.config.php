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
      'expansion' => 'help',
      'description' => '你好',
    ),
  ),
  'command' => array (
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
    'new' => 'NewCommand',
    'help' => 'HelpCommand',
  ),
);
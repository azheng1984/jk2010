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
      'expansion' => array ('help'),
      'description' => '你好',
    ),
  ),
  'sub' => array (
    'make' => array (
      'option' => array (
        'preview' => array (
          'short' => 'p',
          'class' => 'Preview',
          'description' => '',
        ),
      ),
      'infinite_argument',
      'description' => '',
    ),
    'new' => array (
      'sub' => array (
        'web' => array (
          'class' => 'NewWebCommand',
          'option' => array (
            'output_only' => array (
              'short' => 'o',
              'class' => 'OutputNewWebResult',
              'description' => '',
            ),
          ),
        ),
        'cli' => array (
          'class' => 'NewCliCommand',
          'option' => array (
            'output_only' => array (
              'short' => 'o',
              'class' => 'OutputNewCliResultOption',
              'description' => '',
            ),
          ),
        ),
      ),
    ),
    'help' => 'HelpCommand',
  ),
  'default_sub' => array('help'),
);
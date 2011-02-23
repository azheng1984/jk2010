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
          'description' => '',
        ),
        'quite' => array (
          'short' => 'q',
          'description' => '',
        ),
      ),
      'description' => '',
    ),
    'new' => array (
      'sub' => array (
        'web' => array (
          'class' => 'NewWebCommand',
          'option' => array (
            'preview' => array (
            ),
          ),
        ),
        'cli' => array (
          'class' => 'NewCliCommand',
          'option' => array (
            'preview' => array (
              'short' => 'p',
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
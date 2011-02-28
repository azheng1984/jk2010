<?php
return array(
  'option' => array(
    'version' => array(
      'short' => 'v',
      'expansion' => array('help', 'version'),
      'description' => 'print version infomation',
    ),
    'help' => array(
      'short' => array('h', '?'),
      'expansion' => array('help'),
      'description' => 'show help',
    ),
  ),
  'sub' => array(
    'make' => array(
      'option' => array(
        'dry_run' => array(
          'short' => 'd',
          'description' => 'Don’t actually build cache(s), just output',
        ),
      ),
      'description' => '',
    ),
    'new' => array(
      'sub' => array(
        'web' => array(
          'class' => 'NewWebCommand',
          'option' => array(
            'dry_run' => array(
              'short' => 'd',
              'description' => 'Don’t actually build web application scaffold, just output',
            ),
          ),
        ),
        'cli' => array(
          'class' => 'NewCliCommand',
          'option' => array(
            'dry_run' => array(
              'short' => 'd',
              'description' => 'Don’t actually build cli application scaffold, just output',
            ),
          ),
        ),
      ),
    ),
    'help' => 'HelpCommand',
  ),
  'description' => 'hyperframework cli tool',
);
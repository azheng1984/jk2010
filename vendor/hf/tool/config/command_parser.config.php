<?php
return array(
  'option' => array(
    'version' => array(
      'short' => 'v',
      'expansion' => array('help', 'version'),
      'description' => 'Print version infomation',
    ),
    'help' => array(
      'short' => array('h', '?'),
      'expansion' => array('help'),
      'description' => 'Show help',
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
      'class' => 'MakeCommand',
      'description' => 'Compile application',
    ),
    'new' => array(
      'description' => 'Create scaffold',
      'sub' => array(
        'web' => array(
          'class' => 'NewWebCommand',
          'option' => array(
            'dry_run' => array(
              'short' => 'd',
              'description' => 'Don\'t actually build web application scaffold, just output',
            ),
          ),
        ),
        'cli' => array(
          'description' => 'Create new cli application scaffold',
          'class' => 'NewCliCommand',
          'option' => array(
            'dry_run' => array(
              'short' => 'd',
              'description' => 'Don\'t actually build cli application scaffold, just output',
            ),
          ),
        ),
      ),
    ),
    'help' => 'HelpCommand',
  ),
  'description' => 'Hyperframework cli tool version 0.1',
);
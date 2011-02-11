<?php
return array(
  'option' => array(
    'version' => array(
      'short' => 'v',
      'expansion' => array('help', 'version'),
    ),
    'help' => array(
      'short' => array('h', '?'),
      'expansion' => 'help',
    ),
  ),
  'command' => array(
    'make' => array(
      'class' => 'MakeCommand',
      'option' => array(
        'message' => array(
          'short' => 'm',
          'class' => 'MessageOption',
          'infinite_argument',
        ),
        'pagination',
      ),
      'infinite_argument',
    ),
    'new' => 'NewCommand',
    'help' => 'HelpCommand',
  ),
);
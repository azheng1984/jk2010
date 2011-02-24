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
    'help' => 'HelpCommand',
  ),
  'default_sub' => array('help'),
);
<?php return array (
  '/' => 
  array (
    'action' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'after_filter' => true,
    ),
    'views' => 
    array (
      'html' => 'Html',
      'json' => 'Json',
    ),
    'namespace' => 'Hyperframework\\Blog\\App',
  ),
  '/articles' => 
  array (
    'action' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
      ),
    ),
    'views' => 
    array (
      'html' => 'Html',
      'json' => 'Json',
    ),
    'namespace' => 'Hyperframework\\Blog\\App\\Articles',
  ),
  '/articles/item' => 
  array (
    'views' => 
    array (
      'html' => 'Html',
    ),
    'namespace' => 'Hyperframework\\Blog\\App\\Articles\\Item',
  ),
  '/articles/item/comments' => 
  array (
    'views' => 
    array (
      'html' => 'Html',
    ),
    'namespace' => 'Hyperframework\\Blog\\App\\Articles\\Item\\Comments',
  ),
)
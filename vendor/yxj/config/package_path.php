<?php
return array(
    'my.internal',
    array(
        '@base' => 'git://github.com',
        '@folder_mapping' => true,
        'dxl.core' => 'dxl.core',
        'hyperframework.core' => 'hyperframework.core',
        'hyperframework.db' => 'hyperframework.db',
        //'hyperframework/core'
    ),
    'smarty' => array(
        '@version' => '3.2.0',
        '@path' => 'file:///share/smarty-3.2.0',
    ),
    //'smarty' => array('@path' => 'file://share/lib/Smarty3', '@version' => '~1.0.1'),
    'adodb' => 'ftp://internal.com/lib/adodb',
    'dxl.core' => 'git://github.com/dxl/core',
    'hyperframework*' => array('@base' => 'svn://svn.my.org'),
//  'hyperframework.db' => 'svn://svn.my.org/hyperframework/db',
);

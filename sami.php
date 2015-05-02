<?php
// $sami = new Sami\Sami(
//     __DIR__ . '/vendor/hyperframework/hyperframework/lib'
// );

use Symfony\Component\Finder\Finder;
use Sami\Parser\Filter\SymfonyFilter;

$sami = new Sami\Sami(__DIR__ . '/vendor/hyperframework/hyperframework/lib', array(
//     'theme'                => 'enhanced',
    'title'                => 'test',
     'build_dir'            => __DIR__ . '/sami/doc',
     'cache_dir'            => __DIR__ . '/sami/cache',
    'default_opened_level' => 2,
));

// $sami['filter'] = function () {
//     return new SymfonyFilter();
// };

return $sami;